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

class FileAdapter extends AbstractStoreAdapter
{
    private static LoggerInterface $logger;

    public function __construct(string $outputFileName, string $fileSuffix = '', string $customTargetDir = '')
    {
        self::$logger    = new ConsoleLogger(FileAdapter::class);
        self::$logger->debug("START", [$outputFileName,$fileSuffix, $customTargetDir]);

        parent::__construct($outputFileName, $fileSuffix, $customTargetDir);

        self::$logger->debug('END');
    }

    /**
     * @param array<mixed,mixed> $resultsEntry
     */
    public function storeResults(array $resultsEntry): void
    {
        self::$logger->debug('START');

        $pageId      = $resultsEntry[self::KEY_KEY];
        $line        = sprintf(
            '%s;%s%s;%s',
            $pageId,
            $this->constData->c(ConstData::KEY_CONF_BASE_URL),
            $resultsEntry[self::KEY_LINKS][self::KEY_TINYUI],
            $resultsEntry[self::KEY_TITLE]
        );
        // self::$logger->debug('', [$line]);

        self::$logger->debug('Writing results to ', [$this->storeItem]);

        $this->writeData($this->storeItem, sprintf('%s', $line));

        self::$logger->debug('END');
    }

    /**
     * @param mixed $dataContent
     */
    public function storeData(mixed $dataContent): void
    {
        self::$logger->debug('START');
        self::$logger->debug('Writing data to ', [$this->storeItem]);

        $this->writeData($this->storeItem, $dataContent);

        self::$logger->debug('END');
    }

    /**
     * @param string|string[] $dataHeader
     */
    public function storeDataHeader(string|array $dataHeader): void
    {
        self::$logger->debug('START');

        if (!empty($dataHeader)) {
            self::$logger->debug('Writing header to', [$this->storeItem]);
            $this->writeData($this->storeItem, $this->flattenDataHeader($dataHeader));
        }

        self::$logger->debug('END');
    }
}
