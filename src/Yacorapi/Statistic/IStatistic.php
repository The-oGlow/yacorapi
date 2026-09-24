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

use Ds\Vector;

interface IStatistic extends \Stringable
{
    public const string ITEM_SEP = ';';

    /**
     * @return Vector<mixed>
     */
    public function keys(): Vector;

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function keyExists(mixed $key): bool;

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function getItem(mixed $key): mixed;

    /**
     * @param mixed $key
     * @param mixed $item
     */
    public function addItem(mixed $key, mixed $item): void;

    /**
     * Returns the column name for an export.
     *
     * @return string
     */
    public function getExportName(): string;

    /**
     * Returns the name of this statistic element.
     *
     * @return string
     */
    public function getStatisticName(): string;

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
     *
     * @see IStatistic::flattenHeader()
     */
    public function header(): array;

    /**
     * The header will be imploded to a single string.
     *
     * @return string
     *
     * @see IStatistic::header()
     */
    public function flattenHeader(): string;

    /**
     * @inheritDoc
     */
    #[\Override]
    public function __toString(): string;
}
