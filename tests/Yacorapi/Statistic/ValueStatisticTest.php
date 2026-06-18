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
use ollily\Tools\Test\TestData;
use PHPUnit\Framework\EasyGoingTestCase;

class ValueStatisticTest extends EasyGoingTestCase
{
    #[\Override]
    protected function getCasto2t(): ValueStatistic
    {
        return $this->o2t;
    }

    #[\Override]
    protected static function prepareO2t(): ValueStatistic
    {
        return new ValueStatistic(ValueStatistic::EMPTY_STRING, TestData::DATA_NULL);
    }

    public function testKeys(): void
    {
        $expected = new Set([ValueStatistic::KEY_COUNT]);

        $actual = $this->getCasto2t()->keys();

        self::assertEquals($expected, $actual);
    }

    public function testKeyExists(): void
    {
        $actual = $this->getCasto2t()->keyExists(ValueStatistic::KEY_COUNT);

        self::assertTrue($actual);
    }

    public function testGetItem(): void
    {
        $actual = $this->getCasto2t()->getItem(ValueStatistic::EMPTY_STRING);
        self::assertNull($actual);
    }

    public function testAddItem(): void
    {
        $value = TestData::DATA_NUM1;

        $actual = $this->getCasto2t()->getItem(ValueStatistic::EMPTY_STRING);
        self::assertNull($actual);

        $this->getCasto2t()->addItem(ValueStatistic::EMPTY_STRING, $value);

        $actual = $this->getCasto2t()->getItem(ValueStatistic::EMPTY_STRING);

        self::assertEquals($value, $actual);
    }

    public function testHeader(): void
    {
        $expected = [ValueStatistic::KEY_COUNT];

        $actual = $this->getCasto2t()->header();

        self::assertEquals($expected, $actual);
    }

    public function testToString(): void
    {
        $value = TestData::DATA_NUM2;
        $this->getCasto2t()->addItem(ValueStatistic::EMPTY_STRING, $value);

        $expected = sprintf(
            "%s:[%s,{%s}]",
            ValueStatistic::class,
            $this->getCasto2t()->getExportName(),
            $value
        );

        $actual = $this->getCasto2t()->__toString();

        self::assertEquals($expected, $actual);
    }
}
