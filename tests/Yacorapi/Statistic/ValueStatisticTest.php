<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace oglow\tools\Yacorapi\Statistic;

/**
 * Description of ValueStatisticTest
 *

 */
use \PHPUnit\Framework\EasyGoingTestCase;
use ollily\Tools\Test\TestData;

class ValueStatisticTest extends EasyGoingTestCase {

    #[\Override]
    protected function getCasto2t(): ValueStatistic {
        return $this->o2t;
    }

    #[\Override]
    protected static function prepareO2t(): ValueStatistic {
        return new ValueStatistic('A');
    }

    public function testKeys(): void {

        $actual = $this->getCasto2t()->keys();

        self::assertContains(ValueStatistic::EXPORT_NAME, $actual);
    }

    public function tesKeyExists(): void {

        $actual = $this->getCasto2t()->keyExists(ValueStatistic::EXPORT_NAME);

        self::assertTrue($actual);
    }

    public function testGetItem(): void {

        $this->expectException(\BadMethodCallException::class);
        $actual = $this->getCasto2t()->getItem(TestData::NOTEXIST_NAME);
    }

    public function testAddItem(): void {

        $this->expectException(\BadMethodCallException::class);
        $actual = $this->getCasto2t()->addItem(TestData::NOTEXIST_NAME, new ValueStatistic(''));
    }

    public function testGetValue(): void {

        $actual = $this->getCasto2t()->getValue();

        self::assertNull($actual);
    }

    public function testHeader(): void {
        $expected = [ValueStatistic::EXPORT_NAME];
        
        $actual = $this->getCasto2t()->header();
        
        self::assertEquals($expected, $actual);
    }
    
    public function testAddValue(): void {

        $value = TestData::DATA_NUM1;

        $actual = $this->getCasto2t()->getValue();
        self::assertNull($actual);

        $this->getCasto2t()->addValue($value);

        $actual = $this->getCasto2t()->getValue();

        self::assertEquals($value, $actual);
    }

    public function testToString(): void {
        $value = TestData::DATA_NUM2;
        $this->getCasto2t()->addValue($value);

        $expected = sprintf("%s:[%s]",                
                ValueStatistic::class, $value);

        $actual = $this->getCasto2t()->__toString();

        $this->assertEquals($expected, $actual);
    }
}
