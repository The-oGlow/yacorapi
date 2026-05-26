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

namespace oglow\tools\Yacorapi\Data;

use PHPUnit\Framework\EasyGoingTestCase;

class SpaceDataTest extends EasyGoingTestCase
{
    /**
     * @return SpaceData
     */
    protected static function prepareO2t()
    {
        return new SpaceData();
    }

    /**
     * @return SpaceData
     */
    protected function getCasto2t()
    {
        return $this->o2t;
    }

    public function testGetSpaceListDefault(): void
    {
        $result = $this->getCasto2t()->getAllData();
        self::assertIsArray($result);
    }

    public function testGetSpaceListSingle(): void
    {
        $result = $this->getCasto2t()->getDataByMode($this->o2t::SPACE_SINGLE);
        self::assertIsArray($result);
        self::assertCount(1, $result);
    }

    public function testGetSpaceListSimple(): void
    {
        $result = $this->getCasto2t()->getDataByMode($this->o2t::SPACE_SIMPLE);
        self::assertIsArray($result);
        self::assertGreaterThan(1, $result);
    }

    public function testGetSpaceListAll(): void
    {
        $result = $this->getCasto2t()->getDataByMode($this->o2t::SPACE_ALL);
        self::assertIsArray($result);
        self::assertGreaterThan(1, $result);
    }

    public function testPrepareMySpacesFileName(): void
    {
        $expected = $this->getCasto2t()::MY_SPACES_FILE;

        $actual = $this->getCasto2t()::prepareMySpacesFileName();

        self::assertEquals($expected, $actual);
    }

    public function testPrepareMySpacesContent(): void
    {
        $spaces = [];

        $expected1 = $this->getCasto2t()::MY_SPACES_CLAZZ;
        $expected2 = $this->getCasto2t()::MY_SPACES_NS;
        $expected3 = $this->getCasto2t()::SPACE_ALL_METHOD;

        $actual = $this->getCasto2t()::prepareMySpacesContent($spaces);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
        self::assertStringContainsString($expected3, $actual);
    }

    public function testLoadPersonalSpacesFileNotExists(): void
    {
        $mySpaceFile = '';
        $unitTest = true;

        $expected = false;

        $actual = $this->getCasto2t()->loadPersonalSpaces($mySpaceFile, $unitTest);

        self::assertEquals($expected, $actual);
    }
}
