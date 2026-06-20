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

use ollily\Tools\Test\TestData;
use PHPUnit\Framework\EasyGoingTestCase;

class StatisticStatisticTest extends EasyGoingTestCase
{
    private const StatisticTypeEnum STATISTIC_TYPE = StatisticTypeEnum::ADDON;

    private const string EXPORT_NAME = TestData::DATA_ALPHA2 . self::STATISTIC_TYPE->value;

    private const string STATISTIC_NAME = TestData::DATA_ALPHA1;

    #[\Override]
    protected function getCasto2t(): StatisticStatistic
    {
        return $this->o2t;
    }

    #[\Override]
    protected static function prepareO2t(): StatisticStatistic
    {
        return new StatisticStatistic(self::STATISTIC_NAME, self::STATISTIC_TYPE, self::EXPORT_NAME);
    }

    public function testKeys(): void
    {
        $actual = $this->getCasto2t()->keys();

        self::assertEmpty($actual);
    }

    public function testKeyExists(): void
    {
        $actual = $this->getCasto2t()->keyExists(TestData::NOTEXIST_NAME);

        self::assertFalse($actual);
    }

    public function testGetItem(): void
    {
        $actual = $this->getCasto2t()->getItem(TestData::NOTEXIST_NAME);

        self::assertNull($actual);
    }

    public function testAddItem(): void
    {
        $actual = $this->getCasto2t()->getItem(TestData::KEY_ALPHA1);
        self::assertNull($actual);

        $item = $this->prepareSimpleStatistic();
        $this->getCasto2t()->addItem(TestData::KEY_ALPHA1, $item);
        $actual = $this->getCasto2t()->getItem(TestData::KEY_ALPHA1);

        self::assertNotNull($actual);
        self::assertEquals($item, $actual);
    }

    public function testGetExportName(): void
    {
        $expected = self::EXPORT_NAME;

        $actual = $this->getCasto2t()->getExportName();

        self::assertEquals($expected, $actual);
    }

    public function testFlatten(): void
    {
        $item = $this->prepareSimpleStatistic();
        $this->getCasto2t()->addItem(TestData::KEY_NUM1, $item);

        $expected = sprintf(
            "%s=>{%s:[%s,%s]}",
            TestData::KEY_NUM1,
            StatisticStatistic::class,
            StatisticTypeEnum::PAGETYPE->value,
            '{}'
        );

        $actual = $this->getCasto2t()->flatten();

        self::assertEquals($expected, $actual);
    }

    public function testHeader(): void
    {
        $expected = [self::EXPORT_NAME];

        $actual = $this->getCasto2t()->header();

        self::assertEquals($expected, $actual);
    }

    public function testFlattenHeader(): void
    {
        $expected = self::EXPORT_NAME;

        $actual = $this->getCasto2t()->flattenHeader();

        self::assertEquals($expected, $actual);
    }

    public function testToString(): void
    {
        $item = $this->prepareComplexStatistic();
        $this->getCasto2t()->addItem(TestData::KEY_ALPHA1, $item);

        $expected = sprintf(
            "%s:[%s,{{%s}}]",
            StatisticStatistic::class,
            self::EXPORT_NAME,
            $item
        );

        $actual = $this->getCasto2t()->__toString();

        self::assertEquals($expected, $actual);
    }

    private function prepareSimpleStatistic(): IStatistic
    {
        return new StatisticStatistic(self::STATISTIC_NAME, StatisticTypeEnum::PAGETYPE);
    }

    private function prepareComplexStatistic(): IStatistic
    {
        $value = new ValueStatistic(ValueStatistic::EMPTY_STRING, TestData::DATA_NUM5, TestData::KEY_ALPHA2);
        $item = new StatisticStatistic(self::STATISTIC_NAME, StatisticTypeEnum::MACRO);
        $item->addItem(StatisticStatistic::EMPTY_STRING, $value);

        return $item;
    }
}
