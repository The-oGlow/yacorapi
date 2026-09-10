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
use Psr\Log\LoggerInterface;

/**
 * Default implementation for a store item.
 * 
 * @author ollily
 * 
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
class FileStoreItem extends AbstractStoreItem
{
    public const string KEY_DIR  = 'DIR';

    public const string KEY_FILE = 'FILE';

    public const string KEY_EXT  = 'EXT';

    private static LoggerInterface $logger;

    /**
     * @param string $dir  The folder of this store item
     * @param string $file The filename of this store item
     * @param string $ext  The suffix of the filename of this store item
     *
     * @return IStoreItem A newly created store item
     */
    public static function prepareTargetFile(string $dir, string $file, string $ext = StoreParameterData::C_FILE_EXT_TEXT): IStoreItem
    {
        return new self($dir, $file, $ext);
    }

    /**
     * @param string $dir  The folder of this store item
     * @param string $file The filename of this store item
     * @param string $ext  The suffix of the filename of this store item
     * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level           The minimum logging level at which this handler will be triggered (Default: {@link AbstractStoreAdapter::LEVEL_DEFAULT})
     *
     * @phpstan-param LoggingLevel $level
     */
    protected function __construct(string $dir, string $file, string $ext = StoreParameterData::C_FILE_EXT_TEXT,  mixed $level = self::LEVEL_DEFAULT
    )
    {
        self::$logger = new ConsoleLogger(FileStoreItem::class, level: $level);
        self::$logger->debug("START");

        parent::__construct(level: $level);
     
        $this->storeItems->put(self::KEY_DIR, $dir);
        $this->storeItems->put(self::KEY_FILE, $file);
        $ext = str_replace(StoreParameterData::C_FILE_SEP, '', $ext);
        $this->storeItems->put(self::KEY_EXT, $ext);

        self::$logger->debug("END");
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setDir(string $dir): IStoreItem
    {
        $this->storeItems->put(self::KEY_DIR, $dir);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getDir(): string
    {
        return $this->storeItems->get(self::KEY_DIR, '');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setFile(string $file): IStoreItem
    {
        $this->storeItems->put(self::KEY_FILE, $file);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getFile(): string
    {
        return $this->storeItems->get(self::KEY_FILE, '');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setExt(string $ext = StoreParameterData::C_FILE_EXT_TEXT): IStoreItem
    {
        $ext= str_replace(StoreParameterData::C_FILE_SEP, '', $ext);
        $this->storeItems->put(self::KEY_EXT, $ext);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getExt(): string
    {
        return $this->storeItems->get(self::KEY_EXT,StoreParameterData::C_FILE_EXT_TEXT);
    }

    #[\Override]
    public function getStoreName(): string
    {
        return $this->__toString();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function __toString(): string
    {
        return $this->getDir() . DIRECTORY_SEPARATOR . $this->getFile() .  StoreParameterData::C_FILE_SEP. $this->getExt();
    }
}
