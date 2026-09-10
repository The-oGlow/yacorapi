<?php

declare(strict_types=1);

/*
 * This file is part of ezlogging
 *
 * (c) 2024 Oliver Glowa, coding.glowa.com
 *
 * This source file is subject to the Apache-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace oglow\tools\Yacorapi\Store;

use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Abstract implementation for a store adapter.
 * 
 * @author ollily
 * 
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
abstract class AbstractStoreAdapter implements IStoreAdapter
{
    /** Default output level */
    public const string LEVEL_DEFAULT = LogLevel::INFO;

    private static LoggerInterface $logger;
    protected ConstData $constData;
    protected IStoreItem $storeItem;
    private string $sessionFolder;

    /**
     * Constructor for a store adapter.
     * 
     * @param string                                         $fileName  The filename, without suffix, of the output file
     * @param string                                         $filePrefix      Prefix of the output file (Default: {@link StoreParameterData::DEFAULT_FILE_PREFIX})
     * @param string                                         $fileSuffix      Suffix of the output file (Default: {@link StoreParameterData::DEFAULT_FILE_SUFFIX})
     * @param string                                         $fileExt         File extension of the output file (Default: {@link StoreParameterData::DEFAULT_FILE_EXT})
     * @param string                                         $pathToFile      Folder where to store the output file (Default: {@link StoreParameterData::DEFAULT_FOLDER_NAME})
     * @param FileStoreStageEnum                             $staging      The stage where to store the file (Default {@link FileStoreStageEnum::BASE})
     * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level           The minimum logging level at which this handler will be triggered (Default: {@link AbstractStoreAdapter::LEVEL_DEFAULT})
     *
     * @phpstan-param LoggingLevel $level
     */
    public function __construct(
        string $fileName,
        string $filePrefix = StoreParameterData::DEFAULT_FILE_PREFIX,
        string $fileSuffix = StoreParameterData::DEFAULT_FILE_SUFFIX,
        string $fileExt = StoreParameterData::DEFAULT_FILE_EXT,
        string $pathToFile = StoreParameterData::DEFAULT_FOLDER_NAME,
        FileStoreStageEnum $staging = FileStoreStageEnum::BASE,
        mixed $level = self::LEVEL_DEFAULT
    )
    {
        self::$logger = new ConsoleLogger(AbstractStoreAdapter::class, level: $level);
        self::$logger->debug("START", [$fileName, $filePrefix, $fileSuffix, $fileExt, $pathToFile, $staging->name]);

        // Init Dynamic Consts
        $this->constData = new ConstData(AbstractStoreAdapter::class);
        $this->sessionFolder = $this->prepareTargetFolderSession($fileName, $this->constData->c(ConstData::KEY_TARGET_DIR));
        $finalPathToFile = $this->prepareTargetFolderStaging($staging, $this->sessionFolder);
        if (!empty($pathToFile)) {
            $finalPathToFile = $pathToFile;
        }
        $finalFileName = $this->prepareFileName($fileName, $filePrefix, $fileSuffix, $fileExt);

        $this->storeItem = $this->invokeStoreItem($finalFileName, $finalPathToFile);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getStoreItem(): string
    {
        return $this->storeItem->__toString();
    }

    /**
     * Returns the final file name.
     * 
     * @param string $fileName
     * @param string $filePrefix
     * @param string $fileSuffix
     * @param string $fileExt
     * @return string
     */
    protected function prepareFileName(string $fileName, string $filePrefix, string $fileSuffix, string $fileExt): string
    {
        $finalFileName = $fileName;
        if (!empty($filePrefix)) {
            $finalFileName = sprintf('%s-%s', $filePrefix, $finalFileName);
        }
        if (!empty($fileSuffix)) {
            $finalFileName = sprintf('%s-%s', $finalFileName, $fileSuffix);
        }
        if (!empty($fileExt)) {
            $finalFileName = str_replace('..', '.', sprintf('%s.%s', $finalFileName, $fileExt));
        }

        return $finalFileName;
    }

    /**
     * Returns the target folder for this session.
     * 
     * @param string $fileName The filename, without suffix, of the output file
     * @param string $sessionFolder     The current used folder for this session
     *
     * @return string The final full target folder
     */
    protected function prepareTargetFolderSession(string $fileName, string $sessionFolder): string
    {
        self::$logger->debug("START", [$fileName, $sessionFolder]);

        $sessionFolder = $this->prepareTargetFolder($fileName, $sessionFolder);
        $this->mkdir($sessionFolder);

        self::$logger->debug('END');

        return $sessionFolder;
    }

    /**
     * Returns the output file including the full output path<br/>
     * <pre>
     * finalTargetFolder = $pathToFile + filename of $fileName
     * </pre>
     *
     * @param string $fileName The file for output
     * @param string $pathToFile      Folder where to store the output file
     *
     * @return string The complete output folder
     */
    protected function prepareTargetFolder(string $fileName, string $pathToFile): string
    {
        self::$logger->debug('START', [$pathToFile, $fileName]);

        $finalTargetFolder = $pathToFile . DIRECTORY_SEPARATOR . $fileName;

        self::$logger->debug('END', [$finalTargetFolder]);

        return $finalTargetFolder;
    }

    /**
     * Returns the target folder based on the staging.
     * 
     * @param FileStoreStageEnum $staging The stage where to store the file
     * @param string             $sessionDir The current used folder for this session
     *
     * @return string A stage specifix path for the outputfile
     */
    protected function prepareTargetFolderStaging(FileStoreStageEnum $staging, string $sessionDir): string
    {
        self::$logger->debug("START", [$staging->name, $sessionDir]);

        $specialPath = $sessionDir;
        if (!$staging->isDefault()) {
            if (file_exists($sessionDir)) {
                switch ($staging) {
                    case FileStoreStageEnum::ORIGINAL:
                        $specialPath = $this->prepareTargetFolder(ConstData::TARGET_ORGDIR, $sessionDir);
                        $this->mkdir($specialPath);
                        break;
                    case FileStoreStageEnum::MODIFIED:
                        $specialPath = $this->prepareTargetFolder(ConstData::TARGET_MODDIR, $sessionDir);
                        $this->mkdir($specialPath);
                        break;
                    default:
                        break;
                }
            } else {
                self::$logger->warning('Session folder does not exists', [$sessionDir]);
            }
        }
        self::$logger->debug('END', [$specialPath]);

        return $specialPath;
    }

    /**
     * Creates the folder where to store the file.
     * 
     * @param string $directory The folder to create
     *
     * @return bool TRUE=The folder was created, else FALSE
     */
    protected function mkdir(string $directory): bool
    {
        $result = true;
        if (!file_exists($directory)) {
            self::$logger->debug('Create folder', [$directory]);
            $result = mkdir($directory, StoreParameterData::C_DIR_MASK, StoreParameterData::C_DIR_RECURSIVE);
        }

        return $result;
    }

    /**
     * Creates the store item.
     * 
     * @param string $fileName The filename, without suffix, of the output file
     * @param string $pathToFile The folder where to store the output file
     *
     * @return IStoreItem A newly created store item
     */
    protected function invokeStoreItem(string $fileName, string $pathToFile): IStoreItem
    {
        self::$logger->debug("START", [$fileName, $pathToFile]);

        $newClazz = FileStoreItem::prepareTargetFile($pathToFile, pathinfo($fileName, PATHINFO_FILENAME), pathinfo($fileName, PATHINFO_EXTENSION));

        self::$logger->debug('END');

        return $newClazz;
    }

    /**
     * The header for the file will be flatten from array to string.
     * 
     * @param string|string[] $dataHeader The header which will be flatten
     *
     * @return string The header as string
     */
    protected function flattenDataHeader(string|array $dataHeader): string
    {
        self::$logger->debug("START");

        $header = "";
        if (!empty($dataHeader)) {
            if (!is_array($dataHeader)) {
                $dataHeader = [$dataHeader];
            }
            $header = implode(StoreParameterData::DEFAULT_ITEM_SEP, $dataHeader);
        }

        self::$logger->debug('END');

        return $header;
    }

    /**
     * Store any data into the store item.
     * 
     * @param IStoreItem $storeItem The item in which the data will be stored
     * @param mixed      $anyData   The data to store
     */
    protected function writeData(IStoreItem $storeItem, mixed $anyData): void
    {
        self::$logger->debug("START");

        if (!is_null($anyData)) {
            $fileName = $storeItem->__toString();
            self::$logger->debug("Ensure target folder exists", [dirname($fileName)]);
            $this->mkdir(dirname($fileName));
            file_put_contents($fileName, $anyData, FILE_APPEND);
            file_put_contents($fileName, StoreParameterData::C_FILE_EOL, FILE_APPEND);
        }

        self::$logger->debug('END');
    }

    /**
     * @param string $fileName The filename to read in
     *
     * @return array<mixed,mixed> The content of the file
     */
    protected function readResultFile(string $fileName): array
    {
        self::$logger->debug('START', [$fileName]);

        $resultList = [];
        if (file_exists($fileName)) {
            $fHandle = fopen($fileName, StoreParameterData::C_FILE_READ);

            if (!empty($fHandle)) {
                while ($line = fgets($fHandle, StoreParameterData::C_FILE_LINE_LEN)) {
                    $convertedLine = mb_convert_encoding($line, StoreParameterData::C_FILE_UTF8);
                    if (is_string($convertedLine)) { // @phpstan-ignore function.alreadyNarrowedType
                        $resultList[] = explode(StoreParameterData::DEFAULT_ITEM_SEP, $convertedLine);
                    }
                }
                fclose($fHandle);
            }
        } else {
            self::$logger->debug('+++ file does not exists! +++', [$fileName]);
        }

        self::$logger->debug('END', [$fileName]);

        return $resultList;
    }
}
