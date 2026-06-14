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
    public const string DIR  = 'DIR';

    public const string FILE = 'FILE';

    public const string EXT  = 'EXT';

    private static LoggerInterface $logger;

    /**
     * @param string $dir
     * @param string $file
     * @param string $ext
     *
     * @return IStoreItem
     */
    public static function prepareTargetFile(string $dir, string $file, string $ext = IStoreItem::EXT_TEXT): IStoreItem
    {
        return new self($dir, $file, $ext);
    }

    protected function __construct(string $dir, string $file, string $ext = self::EXT_TEXT)
    {
        self::$logger = new ConsoleLogger(FileStoreItem::class);
        self::$logger->debug("START");

        parent::__construct();
        $this->storeItems->put(self::DIR, $dir);
        $this->storeItems->put(self::FILE, $file);
        $this->storeItems->put(self::EXT, $ext);

        self::$logger->debug("END");
    }

    #[\Override]
    public function setDir(string $dir): IStoreItem
    {
        $this->storeItems->put(self::DIR, $dir);

        return $this;
    }

    #[\Override]
    public function getDir(): string
    {
        return $this->storeItems->get(self::DIR, '');
    }

    #[\Override]
    public function setFile(string $file): IStoreItem
    {
        $this->storeItems->put(self::FILE, $file);

        return $this;
    }

    #[\Override]
    public function getFile(): string
    {
        return $this->storeItems->get(self::FILE, '');
    }

    #[\Override]
    public function setExt(string $ext = self::EXT_TEXT): IStoreItem
    {
        $this->storeItems->put(self::EXT, $ext);

        return $this;
    }

    #[\Override]
    public function getExt(): string
    {
        return $this->storeItems->get(self::EXT, self::EXT_TEXT);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getDir() . self::C_PATH_SEP . $this->getFile() . self::C_FILE_SEP . $this->getExt();
    }
}
