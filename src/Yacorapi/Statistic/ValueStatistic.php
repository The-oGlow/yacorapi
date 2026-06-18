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

// use Ds\Set;

/**
 * A statistic element which contains a constant and primitive value;.
 */
class ValueStatistic extends AbstractStatistic
{
    public const string KEY_COUNT = StatisticTypeEnum::VALUE->value;

    /**
     * @param string $statisticName
     * @param mixed  $value
     * @param string $exportName
     */
    public function __construct(string $statisticName, mixed $value, string $exportName = StatisticStatistic::EMPTY_STRING)
    {
        if (empty($statisticName)) {
            $statisticName = self::KEY_COUNT;
        }
        if (empty($exportName)) {
            $exportName = self::KEY_COUNT;
        }
        parent::__construct($statisticName, $exportName, StatisticTypeEnum::VALUE);
        $this->addItem(self::EMPTY_STRING, $value);
    }
    //    private mixed $value = null;

    //    /**
    //     * @inheritDoc
    //     */
    //    #[\Override]
    //    public function keys(): Set {
    //        return new Set([self::EXPORT_NAME]);
    //    }
    //
    //    /**
    //     * @inheritDoc
    //     */
    //    #[\Override]
    //    public function keyExists(mixed $key): bool {
    //        return $key === self::EXPORT_NAME;
    //    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function addItem(mixed $key, mixed $item): void
    {
        parent::addItem(self::KEY_COUNT, $item);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getItem(mixed $key): mixed
    {
        return parent::getItem(self::KEY_COUNT);
    }

    //    public function header(): array {
    //        return [$this->getExportName()];
    //    }

    //    /**
    //     * @inheritDoc
    //     *
    //     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
    //     */
    //    #[\Override]
    //    protected function __toStringValues(): mixed {
    //        return [
    //            $this->getExportName() => $this->getItem(self::KEY_COUNT),
    //        ];
    //    }
}
