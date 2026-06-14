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
    public const string ITEM_SEP = ';';

    public const string KEY_COUNT = 'count';

    /**
     * @return Set<mixed>
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
     * Implode this object and its subitems to a single string with separator.
     *
     * @param bool $displayKeys the items will have their keyname shown
     *
     * @return string
     *
     * @see IStatistic::ITEM_SEP
     */
    public function flatten(bool $displayKeys = true): string;

    /**
     * Give the column names for this object and its subitems as array.
     *
     * @return array<string>
     */
    public function header(): array;

    /**
     * @return string
     */
    public function flattenHeader(): string;

    /**
     * @inheritDoc
     */
    #[\Override]
    public function __toString(): string;
}
