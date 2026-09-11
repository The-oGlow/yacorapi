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
 * Implementation for a file adapter creating csv-files.
 *
 * @author ollly
 *
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
class CsvFileAdapter extends FileAdapter
{
    use ImplodeTrait;

    /** @var string */
    public const string DEFAULT_STORE_ITEM_SUFFIX = StoreParameter::C_FILE_EXT_CSV;

    /** @var array<mixed,mixed> Chars to clean */
    private const array STORDATA_CLEAN = [StoreParameter::DEFAULT_SQUARE_BRACK_OPEN, StoreParameter::DEFAULT_SQUARE_BRACK_CLOSE];

    /** Looking for ";[" */
    private const string STOREDATA_SEARCH = StoreParameter::DEFAULT_ITEM_SEP . StoreParameter::DEFAULT_SQUARE_BRACK_OPEN;

    /** Replacing ";\n[" */
    private const string STOREDATA_REPL = StoreParameter::DEFAULT_ITEM_SEP . StoreParameter::C_FILE_EOL . StoreParameter::DEFAULT_SQUARE_BRACK_OPEN;

    private static LoggerInterface $logger;

    /**
     * Constructor for a store adapter.
     *
     * @param string                                         $fileName   The filename, without suffix, of the output file
     * @param string                                         $filePrefix Prefix of the output file (Default: {@link StoreParameterData::DEFAULT_FILE_PREFIX})
     * @param string                                         $fileSuffix Suffix of the output file (Default: {@link StoreParameterData::DEFAULT_FILE_SUFFIX})
     * @param string                                         $fileExt    File extension of the output file
     *                                                                   (Default: {@link StoreParameterData::DEFAULT_FILE_EXT})
     * @param string                                         $pathToFile Folder where to store the output file
     *                                                                   (Default: {@link StoreParameterData::DEFAULT_FOLDER_NAME})
     * @param FileStoreStageEnum                             $staging    The stage where to store the file (Default {@link FileStoreStageEnum::BASE})
     * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level      The minimum logging level at which this handler will be triggered
     *                                                                   (Default: {@link self::LEVEL_DEFAULT})
     *
     * @phpstan-param LoggingLevel $level
     */
    public function __construct(
        string $fileName,
        string $filePrefix = StoreParameter::DEFAULT_FILE_PREFIX,
        string $fileSuffix = StoreParameter::DEFAULT_FILE_SUFFIX,
        string $fileExt = StoreParameter::DEFAULT_FILE_EXT,
        string $pathToFile = StoreParameter::DEFAULT_FOLDER_NAME,
        FileStoreStageEnum $staging = FileStoreStageEnum::BASE,
        mixed $level = self::LEVEL_DEFAULT
    ) {
        self::$logger = new ConsoleLogger(CsvFileAdapter::class, level: $level);
        self::$logger->debug("START", [$fileName, $filePrefix, $fileSuffix, $fileExt, $pathToFile, $staging->name]);

        if (empty($fileExt)) {
            $finalFileExt = self::DEFAULT_STORE_ITEM_SUFFIX;
        } else {
            $finalFileExt = $fileExt;
        }

        parent::__construct($fileName, $filePrefix, $fileSuffix, $finalFileExt, $pathToFile, $staging, $level);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function storeData(mixed $dataContent): void
    {
        self::$logger->debug('START');

        if (!is_null($dataContent)) {
            $csvLine = self::implode_recursive(StoreParameter::DEFAULT_ITEM_SEP, $dataContent, false, false);
            $csvLine = str_replace(self::STOREDATA_SEARCH, self::STOREDATA_REPL, $csvLine);
            $csvLine = str_replace(self::STORDATA_CLEAN, '', $csvLine);
            $this->writeData($this->storeItem, $csvLine);
        }

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
                $dataHeader[$idx] = StoreParameter::DEFAULT_COLUMN_TEXT_SEP . $dataHeader[$idx] . StoreParameter::DEFAULT_COLUMN_TEXT_SEP;
            }
        }

        self::$logger->debug('END');

        return parent::flattenDataHeader($dataHeader);
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

        return implode(StoreParameter::DEFAULT_ITEM_SEP, $param);
    }
}
