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

interface IStoreItem extends \Stringable
{
    public const string C_FILE_EXT_TEXT = 'txt';

    public const string C_FILE_EXT_CSV = 'csv';

    public const string C_FILE_SEP = '.';

    public const string C_DIR_SEP = DIRECTORY_SEPARATOR;

    /**
     * @param string $dir The new folder of the store item
     *
     * @return IStoreItem The store item
     */
    public function setDir(string $dir): IStoreItem;

    /**
     * @return string The folder of the store item
     */
    public function getDir(): string;

    /**
     * @param string $file The new filename of the store item
     *
     * @return IStoreItem The store item
     */
    public function setFile(string $file): IStoreItem;

    /**
     * @return string The filename of the store item
     */
    public function getFile(): string;

    /**
     * @param string $ext The new suffix of the filename of the store item
     *
     * @return IStoreItem The store item
     */
    public function setExt(string $ext): IStoreItem;

    /**
     * @return string The suffix of the filename of the store item
     */
    public function getExt(): string;

    /**
     * @return string The full filename of the store item
     */
    #[\Override]
    public function __toString(): string;
}
