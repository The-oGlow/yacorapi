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

    /** The unique name if this statistic element */
    public const string STATISTIC_NAME = '';

    /** Defines the column name when exporting */
    public const string EXPORT_NAME = '';

    /** The column name when {@link EXPORT_NAME} is not set */
    public const string UDF = 'undefined';

    /** @var Map<mixed,IStatistic|mixed> */
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

        $this->statisticName = static::STATISTIC_NAME;
        if (!empty($statisticName)) {
            $this->statisticName = $statisticName;
        }
        $this->exportName    = $this->statisticName;
        if (!empty(static::EXPORT_NAME)) {
            $this->exportName = static::EXPORT_NAME;
        }
        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keys(): Set
    {
        return $this->items->keys();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keyExists(string $key): bool
    {
        return !empty($key) && $this->items->hasKey($key);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getItem(string $key): mixed
    {
        $item = null;
        if ($this->keyExists($key)) {
            $item = $this->items[$key];
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function addItem(string $key, mixed $item): void
    {
        $this->items[$key] = $item;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getStatisticName(): string
    {
        return $this->statisticName;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getExportName(): string
    {
        return $this->exportName;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function flatten(bool $displayKeys = true): string
    {
        $flatData = self::implode_recursive(static::ITEM_SEP, $this->items, false, $displayKeys);
        // FIXME: Refactor the output of the ValueStatistic->__toString()
//        if (!is_null($flatData)) {
//            $flatData = preg_replace("/^\{.+\:\[(.+)\]\}$/", "$1", $flatData);
//        }
        if (!is_null($flatData)) {
            $flatData = str_replace('\\"', 'x', $flatData);
        }
//        if (!is_null($flatData)) {
//            $flatData = str_replace('count,value,', '', $flatData);
//        }


        return $flatData;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    #[\Override]
    public function flattenHeader(): string
    {
        $flatten = '';
        $header = $this->header();
        if (!empty($header)) {
            $flatten = self::implode_recursive(static::ITEM_SEP, $header);
        }

        return $flatten;
    }

    /**
     * @inheritDoc
     *
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    #[\Override]
    protected function __toStringValues(): mixed
    {
        return [
            'exportName'    => $this->exportName,
            'items'         => $this->items,
        ];
    }
}
