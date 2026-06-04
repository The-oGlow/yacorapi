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

namespace oglow\tools\Yacorapi\Statistic;

use Ds\Set;

interface IStatistic extends \Stringable
{
    public const ITEM_SEP = ';';

    public const KEY_COUNT = 'count';

    /**
     * @return Set<string>
     */
    public function keys(): Set;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function keyExists(string $key): bool;

    /**
     * @param string $key
     *
     * @return null|IStatistic
     */
    public function getItem(string $key): ?IStatistic;

    /**
     * @param string     $key
     * @param IStatistic $item
     */
    public function addItem(string $key, IStatistic $item): void;

    /**
     * @return string
     */
    public function getStatisticName(): string;

    /**
     * @return string
     */
    public function getExportName(): string;

    /**
     * @param bool $displayKeys
     *
     * @return string
     */
    public function flatten(bool $displayKeys = true): string;

    /**
     * @return array<string>
     */
    public function header(): array;

    //    /**
    //     * @return string
    //     */
    //    public function flattenHeader(): string;

    /**
     * @inheritDoc
     */
    public function __toString();
}
