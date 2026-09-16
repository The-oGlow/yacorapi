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

use Ds\Collection;
use Ds\Sequence;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Response\ResponseParameter as RP;
use oglow\tools\Yacorapi\Space\SpaceInfoEnum;
use oglow\tools\Yacorapi\Store\StoreParameter as SP;
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

    /** @var string Standard file extension for this adapter */
    public const string DEFAULT_FILE_EXT = SP::C_FILE_EXT_CSV;

    /** @var array<mixed,mixed> Chars to clean */
    private const array STORDATA_CLEAN = [SP::DEFAULT_SQUARE_BRACK_OPEN, SP::DEFAULT_SQUARE_BRACK_CLOSE];

    /** Looking for ";[" */
    private const string STOREDATA_SEARCH = SP::DEFAULT_ITEM_SEP . SP::DEFAULT_SQUARE_BRACK_OPEN;

    /** Replacing ";\n[" */
    private const string STOREDATA_REPL = SP::DEFAULT_ITEM_SEP . SP::C_FILE_EOL . SP::DEFAULT_SQUARE_BRACK_OPEN;

    private static LoggerInterface $logger;

    /**
     * Constructor for a store adapter.
     *
     * @param string                                         $fileName   The filename, without suffix, of the output file
     * @param string                                         $filePrefix Prefix of the output file (Default: {@link SP::DEFAULT_FILE_PREFIX})
     * @param string                                         $fileSuffix Suffix of the output file (Default: {@link SP::DEFAULT_FILE_SUFFIX})
     * @param string                                         $fileExt    File extension of the output file
     *                                                                   (Default: {@link SP::DEFAULT_FILE_EXT})
     * @param string                                         $pathToFile Folder where to store the output file
     *                                                                   (Default: {@link SP::DEFAULT_FOLDER_NAME})
     * @param FileStoreStageEnum                             $staging    The stage where to store the file (Default {@link FileStoreStageEnum::BASE})
     * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level      The minimum logging level at which this handler will be triggered
     *                                                                   (Default: {@link self::LEVEL_DEFAULT})
     *
     * @phpstan-param LoggingLevel $level
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
        self::$logger = new ConsoleLogger(CsvFileAdapter::class, level: $level);
        self::$logger->debug("START", [$fileName, $filePrefix, $fileSuffix, $fileExt, $pathToFile, $staging->name]);

        if (empty($fileExt)) {
            $finalFileExt = self::DEFAULT_FILE_EXT;
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
            $csvLine = self::implode_recursive(SP::DEFAULT_ITEM_SEP, $dataContent, false, false);
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
    protected static function flattenDataHeader(string|array $dataHeader): string
    {
        if (is_array($dataHeader)) {
            $headerCount = count($dataHeader);
            for ($idx = 0; $idx < $headerCount; $idx++) {
                $dataHeader[$idx] = SP::DEFAULT_COLUMN_TEXT_SEP . $dataHeader[$idx] . SP::DEFAULT_COLUMN_TEXT_SEP;
            }
        }

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

        return implode(SP::DEFAULT_ITEM_SEP, $param);
    }

    /**
     * Generates a full csv line for the output.
     *
     * @param IResponse       $response      The response
     * @param Sequence<mixed> $exportColumns List of column names to add to the line
     * @param bool            $header        TRUE=generate the file header, else FALSE
     *
     * @phpstan-param Vector<mixed> $exportColumns
     *
     * @return string The full csv line
     */
    public static function prepareExportLine(IResponse $response, Sequence $exportColumns, bool $header = false): string
    {
        $textSep = SP::DEFAULT_COLUMN_TEXT_SEP;
        $sepChar = SP::DEFAULT_ITEM_SEP;

        $line = '';
        if ($exportColumns->count() > 0) {
            if ($header) {
                $line = static::flattenDataHeader($exportColumns->toArray());
            } else {
                foreach ($exportColumns as $exportColumn) {
                    switch ($exportColumn) {
                        case RP::KEY_ID:
                            $line .= $response->getItemId() . $sepChar;
                            break;
                        case RP::KEY_BODY:
                            $line .= $textSep . addslashes($response->getBody()) . $textSep . $sepChar;
                            break;
                        case RP::KEY_SPACE:
                            $line .= $textSep . $response->getSpaceInfo(SpaceInfoEnum::SPACEINFO_KEY) . $textSep . $sepChar;
                            break;
                        case RP::KEY_LABELS:
                            $outLabels = $response->getLabels()->join(self::DEFAULT_GLUE);
                            $line .= $textSep . $outLabels . $textSep . $sepChar;
                            break;
                        default:
                            $outValue = $response->getValue($exportColumn);
                            if (is_array($outValue)) {
                                $outValue = self::implode_recursive(self::DEFAULT_GLUE, $outValue);
                            } elseif (is_object($outValue)) {
                                switch (true) {
                                    case $outValue instanceof Sequence:
                                    case $outValue instanceof Collection:
                                        $outValue = $outValue->toArray();
                                        break;
                                }
                            }
                            $line .= $textSep . $outValue . $textSep . $sepChar;
                            break;
                    }
                }
            }
            if (str_ends_with($line, $sepChar)) {
                $line = substr($line, 0, strlen($sepChar) * -1);
            }
        }

        return $line;
    }
}
