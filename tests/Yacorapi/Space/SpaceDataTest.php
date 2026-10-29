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

namespace oglow\tools\Yacorapi\Space;

use PHPUnit\Framework\EasyGoingTestCase;

class SpaceDataTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): SpaceData
    {
        return new SpaceData();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): SpaceData
    {
        return $this->o2t;
    }

    public function testGetSpaceListDefault(): void
    {
        $expected = 3;

        $actual = $this->getCasto2t()->getAllData();

        self::assertCount($expected, $actual);
    }

    public function testGetSpaceListSingle(): void
    {
        $expected = 1;

        $actual = $this->getCasto2t()->getDataByMode(SpaceTypeEnum::SPACE_SINGLE->value);

        self::assertIsArray($actual);
        self::assertCount($expected, $actual);
    }

    public function testGetSpaceListSimple(): void
    {
        $expected = 1;

        $actual = $this->getCasto2t()->getDataByMode(SpaceTypeEnum::SPACE_SIMPLE->value);

        self::assertIsArray($actual);
        self::assertGreaterThan($expected, $actual);
    }

    public function testGetSpaceListAll(): void
    {
        $expected = 1;

        $actual = $this->getCasto2t()->getDataByMode(SpaceTypeEnum::SPACE_ALL->value);

        self::assertIsArray($actual);
        self::assertGreaterThan($expected, $actual);
    }

    public function testPrepareMySpacesFileName(): void
    {
        $expected = $this->getCasto2t()::VAL_SPACES_FILE;

        $actual = $this->getCasto2t()::prepareMySpacesFileName();

        self::assertEquals($expected, $actual);
    }

    public function testPrepareMySpacesContent(): void
    {
        $spaces = [];

        $expected1 = $this->getCasto2t()::VAL_SPACES_CLAZZ;
        $expected2 = $this->getCasto2t()::VAL_SPACES_NS;
        $expected3 = SpaceTypeEnum::SPACE_ALL->method();

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
