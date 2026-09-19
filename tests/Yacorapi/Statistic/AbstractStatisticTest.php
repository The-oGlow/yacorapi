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

use ollily\Tools\Test\TestData as TeDa;
use PHPUnit\Framework\EasyGoingTestCase;

class AbstractStatisticTest extends EasyGoingTestCase
{
    private const StatisticTypeEnum STATISTIC_TYPE = StatisticTypeEnum::SPACE;

    private const string EXPORT_NAME = TeDa::DATA_ALPHA2 . self::STATISTIC_TYPE->value;

    private const string STATISTIC_NAME = TeDa::DATA_ALPHA1;

    #[\Override]
    protected function getCasto2t(): AbstractStatisticTestDummyClazz
    {
        return $this->o2t;
    }

    #[\Override]
    protected static function prepareO2t(): AbstractStatisticTestDummyClazz
    {
        return new AbstractStatisticTestDummyClazz(self::STATISTIC_NAME, self::EXPORT_NAME, self::STATISTIC_TYPE);
    }

    public function testKeys(): void
    {
        $actual = $this->getCasto2t()->keys();

        self::assertEmpty($actual);
    }

    public function testKeyExists(): void
    {
        $actual = $this->getCasto2t()->keyExists(TeDa::NOTEXIST_NAME);

        self::assertFalse($actual);
    }

    public function testGetItem(): void
    {
        $actual = $this->getCasto2t()->getItem(TeDa::NOTEXIST_NAME);

        self::assertNull($actual);
    }

    public function testAddItem(): void
    {
        $actual = $this->getCasto2t()->getItem(TeDa::KEY_ALPHA1);
        self::assertNull($actual);

        $item = $this->prepareSimpleStatistic();
        $this->getCasto2t()->addItem(TeDa::KEY_ALPHA1, $item);
        $actual = $this->getCasto2t()->getItem(TeDa::KEY_ALPHA1);

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
        $expected = '';

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
        $item = $this->prepareSimpleStatistic();
        $this->getCasto2t()->addItem(TeDa::KEY_ALPHA1, $item);

        $expected = sprintf(
            "%s:[%s,{{%s}}]",
            AbstractStatisticTestDummyClazz::class,
            self::EXPORT_NAME,
            $item
        );

        $actual = $this->getCasto2t()->__toString();

        self::assertEquals($expected, $actual);
    }

    private function prepareSimpleStatistic(): IStatistic
    {
        return new AbstractStatisticTestDummyClazz(self::STATISTIC_NAME, AbstractStatisticTestDummyClazz::EMPTY_STRING, StatisticTypeEnum::PAGETYPE);
    }
}
