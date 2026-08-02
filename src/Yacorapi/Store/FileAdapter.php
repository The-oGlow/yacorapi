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
        self::$logger    = new ConsoleLogger(FileAdapter::class);
        self::$logger->debug("START", [$outputFileName,$fileSuffix, $customTargetDir]);

        parent::__construct($outputFileName, $fileSuffix, $customTargetDir);

        self::$logger->debug('END');
    }

    /**
     * @param array<mixed,mixed> $resultsEntry Array of results from a query
     */
    public function storeResults(array $resultsEntry): void
    {
        self::$logger->debug('START', [$this->storeItem]);

        $pageId      = $resultsEntry[self::KEY_KEY];
        $line        = sprintf(
            '%s;%s%s;%s',
            $pageId,
            $this->constData->c(ConstData::KEY_CONF_BASE_URL),
            $resultsEntry[self::KEY_LINKS][self::KEY_TINYUI],
            $resultsEntry[self::KEY_TITLE]
        );
        $this->writeData($this->storeItem, $line);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function storeData(mixed $dataContent): void
    {
        self::$logger->debug('START', [$this->storeItem]);

        $this->writeData($this->storeItem, $dataContent);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function storeDataHeader(string|array $dataHeader): void
    {
        self::$logger->debug('START', [$this->storeItem]);

        if (!empty($dataHeader)) {
            $this->writeData($this->storeItem, $this->flattenDataHeader($dataHeader));
        }

        self::$logger->debug('END');
    }
}
