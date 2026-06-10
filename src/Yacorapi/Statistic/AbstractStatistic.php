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

    /** Defines the column name when exporting */
    protected const string EXPORT_NAME = '';

    /** The column name when {@link EXPORT_NAME} is not set */
    protected const string UDF = 'undefined';

    /** @var Map<string,IStatistic> */
    private Map $items;

    private string $statisticName;

    private string $exportName;

    private static LoggerInterface $logger;

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
    #[\Override]
    public function keys(): Set
    {
        return $this->items->keys();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    #[\Override]
    public function keyExists(string $key): bool
    {
        return !empty($key) && $this->items->hasKey($key);
    }

    /**
     * @param string $key
     *
     * @return null|IStatistic
     */
    #[\Override]
    public function getItem(string $key): ?IStatistic
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
    #[\Override]
    public function addItem(string $key, IStatistic $item): void
    {
        $this->items[$key] = $item;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getStatisticName(): string
    {
        return $this->statisticName;
    }

    /**
     * @return string
     */
    #[\Override]
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
    #[\Override]
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
    #[\Override]
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

        /**
         * @return string
         */
    #[\Override]
    public function flattenHeader(): string
        {
            //        $header = $this->getExportName() . static::C_ITEM_SEP;
            //        if (!(empty($this->items))) {
            //            $firstItem = $this->items[array_key_first($this->items)];
            //            if ($firstItem instanceof IStatistic) {
            //                $header .= $firstItem->flattenHeader() . static::C_ITEM_SEP;
            //            } else {
            //                $header .= $this->customerHeader() . static::C_ITEM_SEP;
            //            }
            //        }
            $flatten = '';
            $header = $this->header();
            if (!empty($header)) {
                $flatten = $this->implode_recursive(static::C_ITEM_SEP, $header);
                //            $header = str_replace(str_repeat(static::C_ITEM_SEP, 2), static::C_ITEM_SEP, $header);
                //            if (str_ends_with($header, static::C_ITEM_SEP)) {
                //                $header = substr($header, 0, strlen($header) - 1);
                //            }
            }
    
            return $flatten;
        }

    /**
     * @return array<mixed,mixed>
     *
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    #[\Override]
    protected function __toStringValues(): array
    {
        return [
            'statisticName' => $this->statisticName,
            'exportName'    => $this->exportName,
            'items'         => $this->items,
        ];
    }
}
