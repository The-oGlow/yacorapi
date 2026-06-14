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

class CsvFileAdapter extends FileAdapter
{
    use ImplodeTrait;

    private static LoggerInterface $logger;

    public function __construct(string $outputFileName, string $fileSuffix = '', string $customTargetDir = '')
    {
        self::$logger = new ConsoleLogger(CsvFileAdapter::class);
        self::$logger->debug("START");

        parent::__construct($outputFileName, $fileSuffix, $customTargetDir);

        self::$logger->debug('END');
    }

    protected function flattenDataHeader($dataHeader): string
    {
        self::$logger->debug("START");

        if (is_array($dataHeader)) {
            $headerCount = count($dataHeader);
            for ($idx = 0; $idx < $headerCount; $idx++) {
                $dataHeader[$idx] = '"' . $dataHeader[$idx] . '"';
            }
        }

        return parent::flattenDataHeader($dataHeader);
    }

    /**
     * @param array<mixed,mixed>|string $param
     *
     * @return string
     */
    protected function prepareCsvLine(array|string $param): string
    {
        self::$logger->debug("START");

        if (!is_array($param)) {
            $param = [$param];
        }

        return implode(self::C_ITEM_SEP, $param);
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
        string $fileExtension = IStoreItem::EXT_CSV,
        string $storeItemClazz = FileStoreItem::class,
        string $methodName = 'prepareTargetFile'
    ): IStoreItem {
        self::$logger->debug("START");

        if (!str_ends_with($fileExtension, IStoreItem::EXT_CSV)) {
            $fileExtension = $fileExtension . '.' . IStoreItem::EXT_CSV;
        }

        return parent::invokeStoreItem($customTargetDir, $outputFileName, $fileExtension, $storeItemClazz, $methodName);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function storeData(mixed $dataContent): void
    {
        self::$logger->debug('START');
        self::$logger->debug('dataContent', [$dataContent]);

        if (!is_null($dataContent)) {
            $csvLine = self::implode_recursive(self::C_ITEM_SEP, $dataContent, false, false);
            $csvLine = str_replace(self::C_ITEM_SEP . '[', self::C_ITEM_SEP . "\n[", $csvLine);
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
}
