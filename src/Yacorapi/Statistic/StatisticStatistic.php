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

/**
 * A statistic element containing other statistic elements.
 */
class StatisticStatistic extends AbstractStatistic
{
    /**
     * @param string            $statisticName
     * @param StatisticTypeEnum $statisticType
     * @param string            $exportName
     */
    public function __construct(string $statisticName, StatisticTypeEnum $statisticType, string $exportName = StatisticStatistic::EMPTY_STRING)
    {
        if (empty($exportName)) {
            $exportName = $statisticType->value;
        }
        parent::__construct($statisticName, $exportName, $statisticType);
    }
}
