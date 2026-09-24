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

use Ds\Sequence;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\ExitCodes;
use oglow\tools\Yacorapi\Store\StoreParameter as SP;
use ollily\Tools\Emergency;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Abstract implementation for a store adapter.
 *
 * @author ollily
 */
abstract class AbstractStoreAdapter implements IStoreAdapter
{
    private static LoggerInterface $logger;

    protected IStoreItem $storeItem;

    private string $sessionFolder;

    /**
     * Constructor for a store adapter.
     *
     * @param string                 $fileName   The filename, without suffix, of the output file
     * @param string                 $filePrefix Prefix of the output file (Default: {@link SP::DEFAULT_FILE_PREFIX})
     * @param string                 $fileSuffix Suffix of the output file (Default: {@link SP::DEFAULT_FILE_SUFFIX})
     * @param string                 $fileExt    File extension of the output file
     *                                           (Default: {@link SP::DEFAULT_FILE_EXT})
     * @param string                 $pathToFile Folder where to store the output file
     *                                           (Default: {@link SP::DEFAULT_FOLDER_NAME})
     * @param FileStoreStageEnum     $staging    The stage where to store the file (Default {@link FileStoreStageEnum::BASE})
     * @param int|LogLevel::*|string $level      The minimum logging level at which this handler will be triggered (Default: {@link self::LEVEL_DEFAULT})
     */
    public function __construct(
        string $fileName,
        string $filePrefix = SP::DEFAULT_FILE_PREFIX,
        string $fileSuffix = SP::DEFAULT_FILE_SUFFIX,
        string $fileExt = SP::DEFAULT_FILE_EXT,
        string $pathToFile = SP::DEFAULT_FOLDER_NAME,
        FileStoreStageEnum $staging = FileStoreStageEnum::BASE,
        mixed $level = self::LEVEL_DEFAULT
    ) {
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(AbstractStoreAdapter::class, level: $level);
        self::$logger->debug("START", [$fileName, $filePrefix, $fileSuffix, $fileExt, $pathToFile, $staging->name]);

        // Init Dynamic Consts
        /** @psalm-suppress MixedMethodCall */
        $this->sessionFolder = $this->prepareTargetFolderSession($fileName, ConstData::i()->c(ConstData::KEY_TARGET_DIR));
        $finalPathToFile = $this->prepareTargetFolderStaging($staging, $this->sessionFolder);
        if (!empty($pathToFile)) {
            $finalPathToFile = $pathToFile;
        }
        $finalFileName = $this->prepareFileName($fileName, $filePrefix, $fileSuffix, $fileExt);

        $finalPathToFile = str_replace([SP::C_DIR_SEP_WIN, SP::C_DIR_SEP_UNIX], DIRECTORY_SEPARATOR, $finalPathToFile);
        $finalFileName = str_replace([SP::C_DIR_SEP_WIN, SP::C_DIR_SEP_UNIX], DIRECTORY_SEPARATOR, $finalFileName);

        self::$logger->info('Outputfile', [$finalPathToFile, $finalFileName]);
        $this->storeItem = $this->invokeStoreItem($finalFileName, $finalPathToFile);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function readData(string $fileName, bool $withHeader = false): Sequence {
        self::$logger->debug('START', [$fileName, $withHeader]);

        /** @var Sequence<mixed> */
        $resultList = new Vector();
        if (file_exists($fileName)) {
            $fHandle = fopen($fileName, SP::C_FILE_READ);

            if (!empty($fHandle)) {
                $columnHeader = new Vector();
                if ($withHeader) {
                    $columnHeader = self::prepareLineAsColumns(fgets($fHandle, SP::C_FILE_LINE_LEN));
                }
                while ($line = fgets($fHandle, SP::C_FILE_LINE_LEN)) {
                    if (is_string($line)) { // @phpstan-ignore function.alreadyNarrowedType
                        if (empty($columnHeader)) {
                            $resultList->push(self::prepareLineAsColumns($line));
                        } else {
                            $tmpLine = self::prepareLineAsColumns($line);
                            if ($columnHeader->count()==count($tmpLine)) {
                                $resultList->push(array_combine($columnHeader->toArray(), $tmpLine->toArray()));
                            } else {
                                Emergency::breakSystem(
                                        ExitCodes::ERR_CODE_STORE_ADAPTER_COLUMN_DATA_MISMATCH, 
                                        'Column header and column data do not have same size'
                                        );
                            }
                        }
                    }
                }
                fclose($fHandle);
            }
        } else {
            self::$logger->warning('File does not exists', [$fileName]);
        }

        self::$logger->debug('END');
        return $resultList;
    }

    /**
     * Extract the column header from string to array.
     * 
     * @param type $lineHeader The column header as string
     * @return Sequence<mixed> The column header as sequence
     */ 
    protected static function prepareLineAsColumns(string $lineHeader): Sequence {
        $lineHeader = str_replace(SP::C_FILE_EOL_ALL, '', $lineHeader);
        $convertedHeader = mb_convert_encoding($lineHeader, SP::C_FILE_UTF8);

        $columnHeader = new Vector();
        if (is_string($convertedHeader)) { // @phpstan-ignore function.alreadyNarrowedType
            $columnHeader = new Vector(explode(SP::DEFAULT_ITEM_SEP, $convertedHeader));
        } 
        foreach ($columnHeader as $index => $column) {
            $columnHeader->set($index, str_replace(SP::C_ILLEGAL_KEY_CHARS, '', $column));
        }
        return $columnHeader;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getFileName(): string
    {
        return $this->storeItem->getStoreName();
    }

    /**
     * Returns the final file name.
     *
     * @param string $fileName
     * @param string $filePrefix
     * @param string $fileSuffix
     * @param string $fileExt
     *
     * @return string
     */
    protected function prepareFileName(string $fileName, string $filePrefix, string $fileSuffix, string $fileExt): string
    {
        self::$logger->debug("START", [$fileName, $filePrefix, $fileSuffix, $fileExt]);

        $fileName = str_replace([SP::C_DIR_SEP_WIN, SP::C_DIR_SEP_UNIX], DIRECTORY_SEPARATOR, $fileName);
        if (str_contains($fileName, DIRECTORY_SEPARATOR)) {
            $finalFileName = basename($fileName);
        } else {
            $finalFileName = $fileName;
        }

        if (!empty($filePrefix)) {
            $finalFileName = sprintf('%s-%s', $filePrefix, $finalFileName);
        }
        if (!empty($fileSuffix)) {
            $finalFileName = sprintf('%s-%s', $finalFileName, $fileSuffix);
        }
        if (!empty($fileExt)) {
            $finalFileName = str_replace('..', '.', sprintf('%s.%s', $finalFileName, $fileExt));
        }

        self::$logger->debug('END', [$finalFileName]);

        return $finalFileName;
    }

    /**
     * Returns the target folder for this session.
     *
     * @param string $fileName      The filename, without suffix, of the output file
     * @param string $sessionFolder The current used folder for this session
     *
     * @return string The final full target folder
     */
    protected function prepareTargetFolderSession(string $fileName, string $sessionFolder): string
    {
        self::$logger->debug("START", [$fileName, $sessionFolder]);

        $sessionFolder = $this->prepareTargetFolder($fileName, $sessionFolder);
        $this->mkdir($sessionFolder);

        self::$logger->debug('END', [$sessionFolder]);

        return $sessionFolder;
    }

    /**
     * Returns the output file including the full output path<br/>
     * <pre>
     * finalTargetFolder = $pathToFile + filename of $fileName
     * </pre>.
     *
     * @param string $fileName   The file for output
     * @param string $pathToFile Folder where to store the output file
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
     * @param FileStoreStageEnum $staging    The stage where to store the file
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
            $result = mkdir($directory, SP::C_DIR_MASK, SP::C_DIR_RECURSIVE);
        }

        return $result;
    }

    /**
     * Creates the store item.
     *
     * @param string $fileName   The filename, without suffix, of the output file
     * @param string $pathToFile The folder where to store the output file
     *
     * @return IStoreItem A newly created store item
     */
    protected function invokeStoreItem(string $fileName, string $pathToFile): IStoreItem
    {
        self::$logger->debug("START", [$fileName, $pathToFile]);

        $newClazz = FileStoreItem::prepareTargetFile($pathToFile, pathinfo($fileName, PATHINFO_FILENAME), pathinfo($fileName, PATHINFO_EXTENSION));

        self::$logger->debug('END', [$newClazz]);

        return $newClazz;
    }

    /**
     * The header for the file will be flatten from array to string.
     *
     * @param array<mixed>|string $dataHeader The header which will be flatten
     *
     * @return string The header as string
     */
    protected static function flattenDataHeader(array|string $dataHeader): string
    {
        $header = "";
        if (!empty($dataHeader)) {
            if (!is_array($dataHeader)) {
                $dataHeader = [$dataHeader];
            }
            $header = implode(SP::DEFAULT_ITEM_SEP, $dataHeader);
        }

        return $header;
    }

    /**
     * Store any data into the store item. If the store item does not exist, it will be created, including all necessary folders.<br/>
     * If the store item exists, the data will be append at the end.
     *
     * @param IStoreItem $storeItem The item in which the data will be stored
     * @param mixed      $anyData   The data to store
     */
    protected function writeData(IStoreItem $storeItem, mixed $anyData): void
    {
        self::$logger->debug("START");

        if (!is_null($anyData)) {
            $fileName = $storeItem->__toString();
            self::$logger->debug("Doublecheck: Ensure target folder exists", [dirname($fileName)]);
            $this->mkdir(dirname($fileName));
            file_put_contents($fileName, $anyData, FILE_APPEND);
            file_put_contents($fileName, SP::C_FILE_EOL_N, FILE_APPEND);
        }

        self::$logger->debug('END');
    }

}
