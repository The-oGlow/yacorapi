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

namespace oglow\tools\Yacorapi\Response;

use Ds\Map;
use Ds\Vector;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class ResponseTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): Response
    {
        return new Response();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): Response
    {
        return $this->o2t;
    }

    public function testGetResponse(): void
    {
        $expected = Map::class;
        $expectedCount = 0;

        $actual = $this->getCasto2t()->getResponse();

        self::assertInstanceOf($expected, $actual);
        self::assertCount($expectedCount, $actual);
    }

    public function testKeyExists(): void
    {
        $expected = false;

        $actual = $this->getCasto2t()->keyExists(YacorapiTestData::NOTEXIST_ID);

        self::assertEquals($expected, $actual);
    }

    public function testKeys(): void
    {
        $expected = Vector::class;
        $expectedCount = 0;

        $actual = $this->getCasto2t()->keys();

        self::assertInstanceOf($expected, $actual);
        self::assertCount($expectedCount, $actual);
    }

    public function testGetValue(): void
    {
        $expected = YacorapiTestData::KEY_ALPHA2;

        $actual = $this->getCasto2t()->getValue(YacorapiTestData::KEY_ALPHA1, YacorapiTestData::KEY_ALPHA2);

        self::assertEquals($expected, $actual);
    }

    public function testCheckStatus(): void
    {
        $expected = true;

        $actual = $this->getCasto2t()->checkStatus();

        self::assertEquals($expected, $actual);
    }

    public function testCheckData(): void
    {
        $expected = false;

        $actual = $this->getCasto2t()->checkData();

        self::assertEquals($expected, $actual);
    }

    public function testCheckDataWrite(): void
    {
        $expected = false;

        $actual = $this->getCasto2t()->checkDataWrite();

        self::assertEquals($expected, $actual);
    }

    public function testGetResults(): void
    {
        $expected = Map::class;
        $expectedCount = 0;

        $actual = $this->getCasto2t()->getResults();

        self::assertInstanceOf($expected, $actual);
        self::assertCount($expectedCount, $actual);
    }

    public function testGetResult(): void
    {
        $actual = $this->getCasto2t()->getResult(YacorapiTestData::KEY_NUM1);

        self::assertNull($actual);
    }

    public function testIsResultsAvailable(): void
    {
        $expected = false;

        $actual = $this->getCasto2t()->isResultsAvailable();

        self::assertEquals($expected, $actual);
    }

    public function testGetBody(): void
    {
        $expected = '';

        $actual = $this->getCasto2t()->getBody();

        self::assertEquals($expected, $actual);
    }
}
