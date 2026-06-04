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
    public const string EXT_TEXT = 'txt';

    public const string EXT_CSV = 'csv';

    public const C_FILE_SEP = '.';

    public const C_PATH_SEP = DIRECTORY_SEPARATOR;

    /**
     * @param string $dir
     *
     * @return IStoreItem
     */
    public function setDir(string $dir): IStoreItem;

    /**
     * @return string
     */
    public function getDir(): string;

    /**
     * @param string $file
     *
     * @return IStoreItem
     */
    public function setFile(string $file): IStoreItem;

    /**
     * @return string
     */
    public function getFile(): string;

    /**
     * @param string $ext
     *
     * @return IStoreItem
     */
    public function setExt(string $ext): IStoreItem;

    /**
     * @return string
     */
    public function getExt(): string;

    /**
     * @return string
     */
    public function __toString(): string;
}
