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
    public const DIR  = 'DIR';

    public const FILE = 'FILE';

    public const EXT  = 'EXT';

    /** @var LoggerInterface */
    private static $logger;

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

    public function setDir(string $dir): IStoreItem
    {
        $this->storeItems->put(self::DIR, $dir);

        return $this;
    }

    public function getDir(): string
    {
        return $this->storeItems->get(self::DIR, '');
    }

    public function setFile(string $file): IStoreItem
    {
        $this->storeItems->put(self::FILE, $file);

        return $this;
    }

    public function getFile(): string
    {
        return $this->storeItems->get(self::FILE, '');
    }

    public function setExt(string $ext = self::EXT_TEXT): IStoreItem
    {
        $this->storeItems->put(self::EXT, $ext);

        return $this;
    }

    public function getExt(): string
    {
        return $this->storeItems->get(self::EXT, self::EXT_TEXT);
    }

    public function __toString()
    {
        return $this->getDir() . self::C_PATH_SEP . $this->getFile() . self::C_FILE_SEP . $this->getExt();
    }
}
