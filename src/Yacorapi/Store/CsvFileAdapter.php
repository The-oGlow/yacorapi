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
use ollily\Tools\String\ImplodeTrait;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
class CsvFileAdapter extends FileAdapter
{
    use ImplodeTrait;

    public const string DEFAULT_STORE_ITEM_SUFFIX = IStoreItem::C_FILE_EXT_CSV;

    public const string DEFAULT_COLUMN_TEXT_SEP = '"';

    public const string DEFAULT_SQUARE_BRACK_OPEN = '[';

    public const string DEFAULT_SQUARE_BRACK_CLOSE = ']';

    /** @var array<mixed,mixed> Chars to clean */
    private const array STORDATA_CLEAN = [self::DEFAULT_SQUARE_BRACK_OPEN, self::DEFAULT_SQUARE_BRACK_CLOSE];
    
    /** Looking for ";[" */
    private const string STOREDATA_SEARCH = self::DEFAULT_ITEM_SEP . self::DEFAULT_SQUARE_BRACK_OPEN;

    /** Replacing ";\n[" */
    private const string STOREDATA_REPL = self::DEFAULT_ITEM_SEP . self::C_FILE_EOL . self::DEFAULT_SQUARE_BRACK_OPEN;

    private static LoggerInterface $logger;

    /**
     * @param string                                         $outputFileName  The filename, without suffix, of the output file
     * @param string                                         $fileSuffix      An optional suffix of the output file
     * @param string                                         $customTargetDir The folder where to store the output file
     * @param FileStoreStageEnum                             $storeStage      The stage where to store the file (Default {@link FileStoreStageEnum::BASE})
     * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level           The minimum logging level at which this handler will be triggered (Default: {@link AbstractStoreAdapter::LEVEL_DEFAULT})
     *
     * @phpstan-param LoggingLevel $level
     */
    public function __construct(
        string $outputFileName,
        string $fileSuffix = self::DEFAULT_FILE_SUFFIX,
        string $customTargetDir = self::DEFAULT_CUSTOM_TARGET_DIR,
        FileStoreStageEnum $storeStage = FileStoreStageEnum::BASE,
        mixed $level = self::LEVEL_DEFAULT
    ) {
        self::$logger = new ConsoleLogger(CsvFileAdapter::class, level: $level);
        self::$logger->debug("START", [$outputFileName, $fileSuffix, $customTargetDir, $storeStage->name]);

        parent::__construct($outputFileName, $fileSuffix, $customTargetDir, $storeStage, $level);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function flattenDataHeader(string|array $dataHeader): string
    {
        self::$logger->debug("START");

        if (is_array($dataHeader)) {
            $headerCount = count($dataHeader);
            for ($idx = 0; $idx < $headerCount; $idx++) {
                $dataHeader[$idx] = self::DEFAULT_COLUMN_TEXT_SEP . $dataHeader[$idx] . self::DEFAULT_COLUMN_TEXT_SEP;
            }
        }

        self::$logger->debug('END');

        return parent::flattenDataHeader($dataHeader);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function invokeStoreItem(
        string $outputFileName,
        string $finalTargetDir,
        string $fileSuffix = self::DEFAULT_STORE_ITEM_SUFFIX,
        string $storeItemClazz = self::DEFAULT_STORE_ITEM_CLAZZ,
        string $methodName = self::DEFAULT_STORE_ITEM_METHOD
    ): IStoreItem {
        self::$logger->debug("START", [$outputFileName, $finalTargetDir, $fileSuffix, $storeItemClazz, $methodName]);

        if (!str_ends_with($fileSuffix, self::DEFAULT_STORE_ITEM_SUFFIX)) {
            $fileSuffix = $fileSuffix . self::C_FILE_SEP . self::DEFAULT_STORE_ITEM_SUFFIX;
        }

        self::$logger->debug('END');

        return parent::invokeStoreItem($outputFileName, $finalTargetDir, $fileSuffix, $storeItemClazz, $methodName);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function storeData(mixed $dataContent): void
    {
        self::$logger->debug('START');

        if (!is_null($dataContent)) {
            $csvLine = self::implode_recursive(self::DEFAULT_ITEM_SEP, $dataContent, false, false);
            $csvLine = str_replace(self::STOREDATA_SEARCH, self::STOREDATA_REPL, $csvLine);
            $csvLine  = str_replace(self::STORDATA_CLEAN, '',$csvLine);
            $this->writeData($this->storeItem, $csvLine);
        }

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function storeDataHeader(string|array $dataHeader): void
    {
        self::$logger->debug("START");

        if (!empty($dataHeader)) {
            $this->writeData($this->storeItem, $this->flattenDataHeader($dataHeader));
        }

        self::$logger->debug('END');
    }

    /**
     * @param array<mixed,mixed>|string $param
     *
     * @return string
     */
    protected function prepareCsvLine(string|array $param): string
    {
        if (!is_array($param)) {
            $param = [$param];
        }

        return implode(self::DEFAULT_ITEM_SEP, $param);
    }
}
