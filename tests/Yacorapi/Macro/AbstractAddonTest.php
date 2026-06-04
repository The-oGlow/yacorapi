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

namespace oglow\tools\Yacorapi\Macro;

use Ds\Map;
use Ds\Vector;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class AbstractAddonTest extends EasyGoingTestCase
{
    /**
     * @return AbstractAddonTestClazz
     */
    protected static function prepareO2t(): AbstractAddonTestClazz
    {
        return new AbstractAddonTestClazz();
    }

    /**
     * @return AbstractAddonTestClazz
     */
    protected function getCasto2t(): AbstractAddonTestClazz
    {
        return $this->o2t;
    }

    public function testGetAddons(): void
    {
        $expectedCount = 2;
        $expectedType  = Map::class;

        $actual = $this->getCasto2t()->getAddons();

        self::assertCount($expectedCount, $actual);
        self::assertInstanceOf($expectedType, $actual);
    }

    public function testGetAddonNames(): void
    {
        $expected = new Vector([YacorapiTestData::ADDON_1, YacorapiTestData::ADDON_2]);

        $actual = $this->getCasto2t()->getAddonNames();

        self::assertEquals($expected, $actual);
    }

    public function testGetMacros(): void
    {
        $expected = new Vector(
            array_merge(
                YacorapiTestData::ADDON_1_ORDER,
                YacorapiTestData::ADDON_2_ORDER
            )
        );

        $actual = $this->getCasto2t()->getMacros();

        self::assertEquals($expected, $actual);
    }
}
