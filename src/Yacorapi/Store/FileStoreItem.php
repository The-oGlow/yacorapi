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
    public static function prepareTargetFile(string $dir, string $file, string $ext = self::C_FILE_EXT_TEXT): IStoreItem
    {
        return new self($dir, $file, $ext);
    }

    /**
     * @param string $dir  The folder of this store item
     * @param string $file The filename of this store item
     * @param string $ext  The suffix of the filename of this store item
     */
    protected function __construct(string $dir, string $file, string $ext = self::C_FILE_EXT_TEXT)
    {
        self::$logger = new ConsoleLogger(FileStoreItem::class);
        self::$logger->debug("START");

        parent::__construct();
        $this->storeItems->put(self::KEY_DIR, $dir);
        $this->storeItems->put(self::KEY_FILE, $file);
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
    public function setExt(string $ext = self::C_FILE_EXT_TEXT): IStoreItem
    {
        $this->storeItems->put(self::KEY_EXT, $ext);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getExt(): string
    {
        return $this->storeItems->get(self::KEY_EXT, self::C_FILE_EXT_TEXT);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function __toString(): string
    {
        return $this->getDir() . self::C_DIR_SEP . $this->getFile() . self::C_FILE_SEP . $this->getExt();
    }
}
