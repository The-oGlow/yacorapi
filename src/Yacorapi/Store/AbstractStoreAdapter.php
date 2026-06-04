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
    public const ERR_NOT_INVOKED = 30;

    /** Field Separator */
    public const            C_ITEM_SEP = ';';

    protected const         C_DIR_REK = true;

    protected const         C_CODE_UTF8 = 'UTF-8';

    protected const         C_CHAR_LENGTH = 1000;

    protected const         C_FILE_READ = 'r';

    protected const         C_DIR_MASK = 0o777;

    protected ConstData $constData;

    private static LoggerInterface $logger;

    private string $sessionTargetDir;

    protected IStoreItem $storeItem;

    public function __construct(string $outputFileName, string $fileSuffix = '', string $customTargetDir = '')
    {
        self::$logger = new ConsoleLogger(AbstractStoreAdapter::class);
        self::$logger->debug("START", [$outputFileName, $fileSuffix, $customTargetDir]);
        self::$logger->debug("START");

        // Init Dynamic Consts
        $this->constData        = new ConstData(AbstractStoreAdapter::class);
        $this->sessionTargetDir = $this->prepareTargetFolder(
            $outputFileName,
            $this->constData->c(ConstData::KEY_TARGET_ROOTDIR),
            $this->constData->c(ConstData::KEY_TARGET_DIR)
        );
        $this->prepareTargetFolderSpecial($this->sessionTargetDir, ConstData::TARGET_ORGDIR, ConstData::TARGET_MODDIR);
        $this->storeItem = $this->prepareStoreItem($outputFileName, $fileSuffix, $customTargetDir);

        self::$logger->debug('END');
    }

    public function getStoreItem(): string
    {
        return $this->storeItem->__toString();
    }

    protected function prepareTargetFolder(string $outputFileName, string $targetRootDir, string $targetDir): string
    {
        self::$logger->debug('START');

        $sessionDir = $this->constData->prepareFinalTarget($targetDir, $outputFileName);

        self::$logger->debug('create TARGET_ROOT', [$targetRootDir]);
        $this->mkdir($targetRootDir);
        self::$logger->debug('create session folder in TARGET_DIR', [$sessionDir]);
        $this->mkdir($sessionDir);

        self::$logger->debug('END');

        return $sessionDir;
    }

    protected function prepareTargetFolderSpecial(string $sessionDir, string $orgDir, string $modDir): void
    {
        self::$logger->debug('START');

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

    protected function prepareStoreItem(string $outputFileName, string $fileSuffix, string $customTargetDir): IStoreItem
    {
        return $this->invokeStoreItem($customTargetDir, $this->extendNameWithSuffix($outputFileName, $fileSuffix));
    }

    protected function extendNameWithSuffix(string $outputFileName, string $suffix = ''): string
    {
        self::$logger->debug("START");

        $fileName = basename($outputFileName);
        if (!empty($suffix)) {
            $fileName .= '-' . $suffix;
        }

        return $fileName;
    }

    protected function mkdir(string $folder): bool
    {
        if (!file_exists($folder)) {
            return mkdir($folder, self::C_DIR_MASK, self::C_DIR_REK);
        } else {
            return true;
        }
    }

    /**
     * @param string|string[] $dataHeader
     *
     * @return string
     */
    protected function flattenDataHeader(string|array $dataHeader): string
    {
        self::$logger->debug("START");

        $header = "";
        if (!empty($dataHeader)) {
            if (!is_array($dataHeader)) {
                $dataHeader = [$dataHeader];
            }
            $header = implode(self::C_ITEM_SEP, $dataHeader);
        }

        self::$logger->debug('END');

        return $header;
    }

    /**
     * @param IStoreItem $targetFile
     * @param mixed      $anyData
     */
    final protected function writeData(IStoreItem $targetFile, mixed $anyData): void
    {
        self::$logger->debug("START - targetFile", [$targetFile]);

        if (!is_null($anyData)) {
            $targetFolder = dirname($targetFile->__toString());
            $this->mkdir($targetFolder);
            file_put_contents($targetFile->__toString(), $anyData, FILE_APPEND);
            file_put_contents($targetFile->__toString(), "\n", FILE_APPEND);
        }

        self::$logger->debug('END');
    }

    /**
     * @param string $customTargetDir
     * @param string $outputFileName
     * @param string $fileExtension
     * @param string $storeItemClazz
     * @param string $methodName
     *
     * @return IStoreItem
     */
    protected function invokeStoreItem(
        string $customTargetDir,
        string $outputFileName,
        string $fileExtension = IStoreItem::EXT_TEXT,
        string $storeItemClazz = FileStoreItem::class,
        string $methodName = 'prepareTargetFile'
    ): IStoreItem {
        self::$logger->debug("START");

        if (empty($customTargetDir)) {
            $customTargetDir = $this->sessionTargetDir;
        }
        $params = [$customTargetDir, $outputFileName, $fileExtension];

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
     * REFACTOR: no usage so far.
     *
     * @param string $fileName
     *
     * @return array<mixed,mixed>
     *
     * @SuppressWarnings("PHPMD.UnusedPrivateMethod")
     */
    private function readResultFile(string $fileName): array
    {
        self::$logger->debug('START', [$fileName]);

        $resultList = [];
        if (file_exists($fileName)) {
            $fHandle = fopen($fileName, self::C_FILE_READ);

            if (!empty($fHandle)) {
                while ($line = fgets($fHandle, self::C_CHAR_LENGTH)) {
                    $convertedLine = mb_convert_encoding($line, self::C_CODE_UTF8);
                    if (is_string($convertedLine)) {
                        $resultList[] = explode(self::C_ITEM_SEP, $convertedLine);
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
