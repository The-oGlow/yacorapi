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
use ollily\Tools\Emergency;
use Psr\Log\LoggerInterface;

abstract class AbstractStoreAdapter implements IStoreAdapter
{
    /** Field Separator */
    public const string DEFAULT_ITEM_SEP = ';';

    public const string DEFAULT_FILE_SUFFIX = '';

    public const string DEFAULT_CUSTOM_TARGET_DIR = '';

    public const string DEFAULT_STORE_ITEM_CLAZZ = FileStoreItem::class;

    public const string DEFAULT_STORE_ITEM_METHOD = 'prepareTargetFile';

    public const string DEFAULT_STORE_ITEM_SUFFIX = IStoreItem::C_FILE_EXT_TEXT;

    public const int ERR_NOT_INVOKED = 30;

    protected const bool C_DIR_RECURSIVE = true;

    protected const int C_DIR_MASK = 0o777;

    protected const string C_FILE_UTF8 = 'UTF-8';

    protected const int C_FILE_LINE_LEN = 1000;

    protected const string C_FILE_READ = 'r';

    protected const string C_FILE_SEP = '.';

    protected const string C_FILE_EOL = "\n";

    private static LoggerInterface $logger;

    protected ConstData $constData;

    protected IStoreItem $storeItem;

    private string $sessionTargetDir;

    /**
     * @param string $outputFileName  The filename, without suffix, of the output file
     * @param string $fileSuffix      An optional suffix of the output file
     * @param string $customTargetDir The folder where to store the output file
     */
    public function __construct(
        string $outputFileName,
        string $fileSuffix = self::DEFAULT_FILE_SUFFIX,
        string $customTargetDir = self::DEFAULT_CUSTOM_TARGET_DIR
    ) {
        self::$logger = new ConsoleLogger(AbstractStoreAdapter::class);
        self::$logger->debug("START", [$outputFileName, $fileSuffix, $customTargetDir]);

        // Init Dynamic Consts
        $this->constData = new ConstData(AbstractStoreAdapter::class);
        $this->sessionTargetDir = $this->prepareTargetFolder(
            $outputFileName,
            $this->constData->c(ConstData::KEY_TARGET_ROOTDIR),
            $this->constData->c(ConstData::KEY_TARGET_DIR)
        );
        $this->prepareTargetFolderSpecial($this->sessionTargetDir, ConstData::TARGET_ORGDIR, ConstData::TARGET_MODDIR);
        $this->storeItem = $this->prepareStoreItem($outputFileName, $fileSuffix, $customTargetDir);

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
     * @param string $outputFileName The filename, without suffix, of the output file
     * @param string $targetRootDir  The folder of all output files
     * @param string $sessionDir     The current used folder for this session
     *
     * @return string The final full target folder
     */
    protected function prepareTargetFolder(string $outputFileName, string $targetRootDir, string $sessionDir): string
    {
        self::$logger->debug("START", [$outputFileName, $targetRootDir, $sessionDir]);

        $sessionDir = $this->constData->prepareFinalTarget($sessionDir, $outputFileName);

        self::$logger->debug('create TARGET_ROOT', [$targetRootDir]);
        $this->mkdir($targetRootDir);
        self::$logger->debug('create session folder in TARGET_DIR', [$sessionDir]);
        $this->mkdir($sessionDir);

        self::$logger->debug('END');

        return $sessionDir;
    }

    /**
     * @param string $sessionDir The current used folder for this session
     * @param string $orgDir     The folder name where to store the original files
     * @param string $modDir     the folder name where to store the modified files
     */
    protected function prepareTargetFolderSpecial(string $sessionDir, string $orgDir, string $modDir): void
    {
        self::$logger->debug("START", [$sessionDir, $orgDir, $modDir]);

        if (file_exists($sessionDir)) {
            $targetOrgDir = $this->constData->prepareFinalTarget($sessionDir, $orgDir);
            $targetModDir = $this->constData->prepareFinalTarget($sessionDir, $modDir);

            self::$logger->debug('create TARGET_ORG_DIR & TARGET_MOD_DIR', [$targetOrgDir, $targetModDir]);
            $this->mkdir($targetOrgDir);
            $this->mkdir($targetModDir);
        } else {
            self::$logger->warning('session folder does not exists!', [$sessionDir]);
        }

        self::$logger->debug('END');
    }

    /**
     * @param string $outputFileName  The filename, without suffix, of the output file
     * @param string $fileSuffix      An optional suffix of the output file
     * @param string $customTargetDir The folder where to store the output file
     *
     * @return IStoreItem A newly created store item
     */
    protected function prepareStoreItem(string $outputFileName, string $fileSuffix, string $customTargetDir): IStoreItem
    {
        return $this->invokeStoreItem($customTargetDir, $this->extendNameWithSuffix($outputFileName, $fileSuffix));
    }

    /**
     * @param string $outputFileName The filename, without suffix, of the output file
     * @param string $fileSuffix     An optional suffix of the output file
     *
     * @return string A filename with an optional suffix
     */
    protected function extendNameWithSuffix(string $outputFileName, string $fileSuffix = self::DEFAULT_FILE_SUFFIX): string
    {
        $fileName = basename($outputFileName);
        if (!empty($fileSuffix)) {
            $fileName .= '-' . $fileSuffix;
        }

        return $fileName;
    }

    /**
     * @param string $directory The folder to create
     *
     * @return bool TRUE=The folder was created, else FALSE
     */
    protected function mkdir(string $directory): bool
    {
        $result = true;
        if (!file_exists($directory)) {
            $result = mkdir($directory, self::C_DIR_MASK, self::C_DIR_RECURSIVE);
        }

        return $result;
    }

    /**
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
            $header = implode(self::DEFAULT_ITEM_SEP, $dataHeader);
        }

        self::$logger->debug('END');

        return $header;
    }

    /**
     * @param IStoreItem $storeItem The item in which the data will be stored
     * @param mixed      $anyData   The data to store
     */
    protected function writeData(IStoreItem $storeItem, mixed $anyData): void
    {
        self::$logger->debug("START");

        if (!is_null($anyData)) {
            $targetFolder = dirname($storeItem->__toString());
            $this->mkdir($targetFolder);
            file_put_contents($storeItem->__toString(), $anyData, FILE_APPEND);
            file_put_contents($storeItem->__toString(), self::C_FILE_EOL, FILE_APPEND);
        }

        self::$logger->debug('END');
    }

    /**
     * @param string $customTargetDir The folder where to store the output file
     * @param string $outputFileName  The filename, without suffix, of the output file
     * @param string $fileSuffix      An optional suffix of the output file
     * @param string $storeItemClazz  The class of the storeItem
     * @param string $methodName      The method of the storeItem to create the store item
     *
     * @return IStoreItem A newly created store item
     */
    protected function invokeStoreItem(
        string $customTargetDir,
        string $outputFileName,
        string $fileSuffix = self::DEFAULT_STORE_ITEM_SUFFIX,
        string $storeItemClazz = self::DEFAULT_STORE_ITEM_CLAZZ,
        string $methodName = self::DEFAULT_STORE_ITEM_METHOD
    ): IStoreItem {
        self::$logger->debug("START");

        if (empty($customTargetDir)) {
            $customTargetDir = $this->sessionTargetDir;
        }
        $params = [$customTargetDir, $outputFileName, $fileSuffix];

        try {
            /**
             * @phpstan-ignore staticMethod.dynamicName
             */
            $newClazz = $storeItemClazz::$methodName(...$params);
        } catch (\Exception $e) {
            Emergency::breakSystem(self::ERR_NOT_INVOKED, $e->getMessage());
        }

        self::$logger->debug('END');

        /**
         * @psalm-suppress PossiblyUndefinedVariable
         * @phpstan-ignore variable.undefined
         */
        return $newClazz;
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
            $fHandle = fopen($fileName, self::C_FILE_READ);

            if (!empty($fHandle)) {
                while ($line = fgets($fHandle, self::C_FILE_LINE_LEN)) {
                    $convertedLine = mb_convert_encoding($line, self::C_FILE_UTF8);
                    if (is_string($convertedLine)) { // @phpstan-ignore function.alreadyNarrowedType
                        $resultList[] = explode(self::DEFAULT_ITEM_SEP, $convertedLine);
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
