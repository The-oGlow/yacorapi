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

namespace oglow\tools\common;

interface IContainer extends \Stringable
{
    /**
     * @return mixed[]
     */
    public function getAllData(): array;

    /**
     * @return mixed[]
     */
    public function getKeys(): array;

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function keyExists($key): bool;

    /**
     * @return int[]|string[]
     */
    public function getModes(): array;

    /**
     * @param int|string $mode
     *
     * @return mixed
     */
    public function getDataByMode($mode);

    /**
     * @inheritdoc
     */
    public function __toString();
}
