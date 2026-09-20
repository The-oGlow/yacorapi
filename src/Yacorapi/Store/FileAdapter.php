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
use oglow\tools\Yacorapi\Store\StoreParameter as SP;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Implementation for a standarf file adapter.
 *
 * @author ollily
 *
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
class FileAdapter extends AbstractStoreAdapter
{
    /** @var string Standard file extension for this adapter */
    public const string DEFAULT_FILE_EXT = SP::C_FILE_EXT_TEXT;

    private static LoggerInterface $logger;

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
        self::$logger    = new ConsoleLogger(FileAdapter::class, level: $level);
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
        self::$logger->debug('START', [$this->storeItem]);

        $this->writeData($this->storeItem, $dataContent);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function storeDataHeader(array|string $dataHeader): void
    {
        self::$logger->debug('START', [$this->storeItem]);

        if (!empty($dataHeader)) {
            $this->writeData($this->storeItem, static::flattenDataHeader($dataHeader));
        }

        self::$logger->debug('END');
    }

    /**
     * @param array<mixed> $resultsEntry Array of results from a query
     */
    public function storeResults(array $resultsEntry): void
    {
        self::$logger->debug('START', [$this->storeItem]);

        /** @psalm-suppress MixedMethodCall */
        $line = sprintf(
            '%s;%s%s;%s',
            $resultsEntry[SP::KEY_KEY],
            ConstData::i()->c(ConstData::KEY_CONF_BASE_URL),
            $resultsEntry[SP::KEY_LINKS][SP::KEY_TINYUI],
            $resultsEntry[SP::KEY_TITLE]
        );
        $this->writeData($this->storeItem, $line);

        self::$logger->debug('END');
    }
}
