<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace oglow\tools\Yacorapi\Statistic;

use PHPUnit\Framework\EasyGoingTestCase;
use ollily\Tools\Test\TestData;

class AbstractStatisticTest extends EasyGoingTestCase {

    #[\Override]
    protected function getCasto2t(): AbstractStatisticTestDummyClazz {
        return $this->o2t;
    }

    #[\Override]
    protected static function prepareO2t(): AbstractStatisticTestDummyClazz {
        return new AbstractStatisticTestDummyClazz('');
    }

    public function testKeys(): void {

        $actual = $this->getCasto2t()->keys();

        self::assertEmpty($actual);
    }

    public function testKeyExist(): void {

        $actual = $this->getCasto2t()->keyExists(TestData::NOTEXIST_NAME);

        self::assertFalse($actual);
    }

    public function testGetItem(): void {

        $actual = $this->getCasto2t()->getItem(TestData::NOTEXIST_NAME);

        self::assertNull($actual);
    }

    public function testAddItem(): void {

        $actual = $this->getCasto2t()->getItem(TestData::KEY_ALPHA1);
        self::assertNull($actual);

        $item = $this->prepareO2t();
        $this->getCasto2t()->addItem(TestData::KEY_ALPHA1, $item);
        $actual = $this->getCasto2t()->getItem(TestData::KEY_ALPHA1);

        self::assertNotNull($actual);
        self::assertEquals($item, $actual);
    }

    public function testGetExportName(): void {
        $expected = AbstractStatisticTestDummyClazz::EXPORT_NAME;

        $actual = $this->getCasto2t()->getExportName();

        self::assertEquals($expected, $actual);
    }

    public function testGetStatisticName(): void {
        $expected = AbstractStatisticTestDummyClazz::STATISTIC_NAME;

        $actual = $this->getCasto2t()->getStatisticName();

        self::assertEquals($expected, $actual);
    }

    public function testFlatten(): void {

        $expected = '';

        $actual = $this->getCasto2t()->flatten();

        self::assertEquals($expected, $actual);
    }

    public function testHeader(): void {

        $expected = [AbstractStatisticTestDummyClazz::EXPORT_NAME];

        $actual = $this->getCasto2t()->header();

        self::assertEquals($expected, $actual);
    }

    public function testFlattenHeader(): void {

        $expected = AbstractStatisticTestDummyClazz::EXPORT_NAME;

        $actual = $this->getCasto2t()->flattenHeader();

        self::assertEquals($expected, $actual);
    }

    public function testToString(): void {
        $expected = sprintf("%s:[%s,%s,%s]",
                AbstractStatisticTestDummyClazz::class,
                AbstractStatisticTestDummyClazz::STATISTIC_NAME,
                AbstractStatisticTestDummyClazz::EXPORT_NAME,
                '{}');

        $actual = $this->getCasto2t()->__toString();

        $this->assertEquals($expected, $actual);
    }
}
