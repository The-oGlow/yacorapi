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

use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class AllAddonTest extends EasyGoingTestCase
{
    /**
     * @return AllAddon
     */
    protected static function prepareO2t(): AllAddon
    {
        return new AllAddon();
    }

    /**
     * @return AllAddon
     */
    protected function getCasto2t(): AllAddon
    {
        return $this->o2t;
    }

    public function testGetAddons(): void
    {
        $expectedSize  = YacorapiTestData::ADDONS_COUNT_TOTAL;
        $expectedAddon = YacorapiTestData::ADDON_VERIFY;

        $actual = $this->getCasto2t()->getAddons();

        self::assertCount($expectedSize, $actual);
        self::assertArrayHasKey($expectedAddon, $actual);
    }

    public function testGetAddonNames(): void
    {
        $expectedSize  = YacorapiTestData::ADDONS_COUNT_TOTAL;
        $expectedAddon = YacorapiTestData::ADDON_VERIFY;

        $actual = $this->getCasto2t()->getAddonNames();

        self::assertCount($expectedSize, $actual);
        self::assertContains($expectedAddon, $actual);
    }

    public function testGetMacros(): void
    {
        $expectedSize  = YacorapiTestData::MACROS_COUNT_TOTAL;
        $expectedMacro = YacorapiTestData::MACRO_VERIFY;

        $actual = $this->getCasto2t()->getMacros();

        self::assertCount($expectedSize, $actual);
        self::assertContains($expectedMacro, $actual);
    }

    public function testGetMacrosArray(): void
    {
        $expectedSize  = YacorapiTestData::MACROS_COUNT_TOTAL;
        $expectedMacro = YacorapiTestData::MACRO_VERIFY;

        $actual = $this->getCasto2t()->getMacrosArray();

        self::assertCount($expectedSize, $actual);
        self::assertContains($expectedMacro, $actual);
    }
}
