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

use Ds\Map;
use Ds\Pair;
use Ds\Set;
use Monolog\ConsoleLogger;
use ollily\Tools\String\ToStringTrait;
use Psr\Log\LoggerInterface;

abstract class AbstractStatistic implements IStatistic
{
    use ToStringTrait;

    /** @var string Defines the column name when exporting */
    protected const EXPORT_NAME = '';

    /** @var string The column name when {@link EXPORT_NAME} is not set */
    protected const UDF = 'undefined';

    /** @var Map<string,IStatistic> */
    private $items;

    /** @var string */
    private $statisticName;

    /** @var string */
    private $exportName;

    /** @var LoggerInterface */
    private static $logger;

    /**
     * @param string $statisticName
     */
    public function __construct(string $statisticName)
    {
        self::$logger = new ConsoleLogger(AbstractStatistic::class);
        self::$logger->debug('START');

        $this->items = new Map([]);

        $this->statisticName = $statisticName;
        $this->exportName    = $this->statisticName;
        if (!empty(static::EXPORT_NAME)) {
            $this->exportName = static::EXPORT_NAME;
        }
        self::$logger->debug('END');
    }

    /**
     * @return Set<string>
     */
    public function keys(): Set
    {
        return $this->items->keys();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function keyExists($key): bool
    {
        return !empty($key) && $this->items->hasKey($key);
    }

    /**
     * @param string $key
     *
     * @return null|IStatistic
     */
    public function getItem($key)
    {
        $item = null;
        if ($this->keyExists($key)) {
            $item = $this->items[$key];
        }

        return $item;
    }

    /**
     * @param string     $key
     * @param IStatistic $item
     */
    public function addItem($key, $item): void
    {
        $this->items[$key] = $item;
    }

    /**
     * @return string
     */
    public function getStatisticName(): string
    {
        return $this->statisticName;
    }

    /**
     * @return string
     */
    public function getExportName(): string
    {
        return $this->exportName;
    }

    /**
     * Implode this object and its subitems to a single string with separator.
     *
     * @param bool $displayKeys the items will have their keyname shown
     *
     * @return string
     *
     * @see IStatistic::ITEM_SEP
     */
    public function flatten(bool $displayKeys = true): string
    {
        $flatData = $this->implode_recursive(static::ITEM_SEP, $this->items, false, $displayKeys);
        $flatData = str_replace('\\"', 'x', $flatData);

        self::$logger->debug('', [$flatData]);

        return $flatData;
    }

    /**
     * Give the column names for this object and its subitems as array.
     *
     * @return array<string>
     */
    public function header(): array
    {
        $header   = [];
        $header[] = $this->getExportName();

        if (!$this->items->isEmpty()) {
            /** @var Pair<string,mixed> $firstItem */
            $firstItem = $this->items->first();
            /** @var null|IStatistic $value */
            $value = $firstItem->value;
            if (!empty($value)) {
                $header = array_merge($header, $value->header());
            } else {
                array_push($header, self::UDF);
            }
        }

        return $header;
    }

    //    /**
    //     * @return string
    //     */
    //    public function flattenHeader(): string
    //    {
    //        //        $header = $this->getExportName() . static::C_ITEM_SEP;
    //        //        if (!(empty($this->items))) {
    //        //            $firstItem = $this->items[array_key_first($this->items)];
    //        //            if ($firstItem instanceof IStatistic) {
    //        //                $header .= $firstItem->flattenHeader() . static::C_ITEM_SEP;
    //        //            } else {
    //        //                $header .= $this->customerHeader() . static::C_ITEM_SEP;
    //        //            }
    //        //        }
    //        $flatten = '';
    //        $header = $this->header();
    //        if (!empty($header)) {
    //            $flatten = $this->implode_recursive(static::C_ITEM_SEP, $header);
    //            //            $header = str_replace(str_repeat(static::C_ITEM_SEP, 2), static::C_ITEM_SEP, $header);
    //            //            if (str_ends_with($header, static::C_ITEM_SEP)) {
    //            //                $header = substr($header, 0, strlen($header) - 1);
    //            //            }
    //        }
    //
    //        return $flatten;
    //    }

    /**
     * @return array<mixed,mixed>
     *
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    protected function __toStringValues(): array
    {
        return [
            'statisticName' => $this->statisticName,
            'exportName'    => $this->exportName,
            'items'         => $this->items
        ];
    }
}
