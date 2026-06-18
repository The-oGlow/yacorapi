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

    /** The column name when {@link EXPORT_NAME} is not set */
    public const string UDF = 'undefined';

    /** Use this key to use the default export name */
    public const string EMPTY_STRING = '';

    private static LoggerInterface $logger;

    /** @var Map<mixed,mixed> All chuld statistic elements on this element */
    private Map $items;

    /** The name of the statistic element */
    private string $statisticName;

    /** Defines the column name when exporting */
    private string $exportName = '';

    /** Defines the type of statistic element.
     * @phpstan-ignore property.onlyWritten
     */
    private StatisticTypeEnum $statisticType;

    /**
     * @param string            $statisticName The name of the statistic element
     * @param string            $exportName    The column name for an export
     * @param StatisticTypeEnum $statisticType The type of the statistic
     */
    public function __construct(string $statisticName, string $exportName, StatisticTypeEnum $statisticType)
    {
        self::$logger = new ConsoleLogger(AbstractStatistic::class);
        self::$logger->debug('START');

        $this->items = new Map([]);

        $this->statisticType = $statisticType;
        if (!empty($exportName)) {
            $this->exportName = $exportName;
        }
        $this->statisticName = $statisticName;
        if (empty($this->statisticName)) {
            $this->statisticName = self::UDF;
        }
        if (empty($this->exportName)) {
            $this->exportName = self::UDF;
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
    public function keyExists(mixed $key): bool
    {
        return !empty($key) && $this->items->hasKey($key);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getItem(mixed $key): mixed
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
    public function addItem(mixed $key, mixed $item): void
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
        if (!is_null($flatData)) { // @phpstan-ignore function.impossibleType
            $flatData = preg_replace("/^\{.+\:\[(.+)\]\}$/", "$1", $flatData);
        }
        if (!is_null($flatData)) {
            $flatData = str_replace('\\"', 'x', $flatData);
        }
        if (!is_null($flatData)) {
            $flatData = str_replace('count,value,', '', $flatData);
        }

        return $flatData; // @phpstan-ignore return.type
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
            /** @var mixed $value */
            $value = $firstItem->value;
            if (!empty($value) && $value instanceof IStatistic) {
                $header = array_merge($header, $value->header());
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
