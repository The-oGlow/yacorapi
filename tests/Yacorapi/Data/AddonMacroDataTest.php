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

use oglow\tools\Yacorapi\Macro\AllAddon;
use oglow\tools\Yacorapi\Macro\BlockerAddon;
use oglow\tools\Yacorapi\Macro\SingleAddon;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class AddonMacroDataTest extends EasyGoingTestCase
{
    /**
     * @return AddonMacroData
     */
    protected static function prepareO2t(): AddonMacroData
    {
        return new AddonMacroData();
    }

    /**
     * @return AddonMacroData
     */
    protected function getCasto2t(): AddonMacroData
    {
        return $this->o2t;
    }

    /**
     * @param int             $expected
     * @param null|int|string $mode
     *
     * @dataProvider providerGetMacro
     */
    public function testGetMacros(int $expected, int|string|null $mode = null): void
    {
        if (is_null($mode)) {
            $actual = $this->getCasto2t()->getMacros();
        } else {
            $actual = $this->getCasto2t()->getMacros($mode);
        }
        self::assertIsArray($actual);
        self::assertCount($expected, $actual);
    }

    /**
     * @return array<string,array<int,mixed>>
     */
    public function providerGetMacro(): array
    {
        return [
            'DefaultMode' => [YacorapiTestData::MODE_SINGLE_MACRO_COUNT_TOTAL, null],
            'SingleMode'  => [YacorapiTestData::MODE_SINGLE_MACRO_COUNT_TOTAL, SingleAddon::ADDON_SINGLE],
            'BlockerMode' => [YacorapiTestData::MODE_BLOCKER_MACRO_COUNT_TOTAL, BlockerAddon::ADDON_BLOCKER],
            'AllMode'     => [YacorapiTestData::MODE_ALL_MACRO_COUNT_TOTAL, AllAddon::ADDON_ALL],
            'WrongMode'   => [0, YacorapiTestData::MODE_NOTEXIST],
        ];
    }

    /**
     * @param int        $expected
     * @param int|string $mode
     * @param string     $addon
     *
     * @dataProvider providerGetMacroNamesByAddon
     */
    public function testgetMacroNamesByAddon(int $expected, int|string $mode = '', string $addon = ''): void
    {
        $actual = $this->getCasto2t()->getMacroNamesByAddon($mode, $addon);

        self::assertIsArray($actual);
        self::assertCount($expected, $actual);
    }

    /**
     * @return array<string,array<int,mixed>>
     */
    public function providerGetMacroNamesByAddon(): array
    {
        return [
            'SingleMode'          => [
                YacorapiTestData::MODE_SINGLE_ADDON_NAME_MACRO_COUNT,
                SingleAddon::ADDON_SINGLE,
                YacorapiTestData::MODE_SINGLE_ADDON_NAME,
            ],
            'SingleModeNotExist'  => [
                YacorapiTestData::MODE_SINGLE_ADDON_NAME_NOTEXIST_MACRO_COUNT,
                SingleAddon::ADDON_SINGLE,
                YacorapiTestData::MODE_SINGLE_ADDON_NAME_NOTEXIST,
            ],
            'BlockerMode'         => [
                YacorapiTestData::MODE_BLOCKER_ADDON_NAME_MACRO_COUNT,
                BlockerAddon::ADDON_BLOCKER,
                YacorapiTestData::MODE_BLOCKER_ADDON_NAME,
            ],
            'BlockerModeNotExist' => [
                YacorapiTestData::MODE_BLOCKER_ADDON_NAME_NOTEXISTS_MACRO_COUNT,
                BlockerAddon::ADDON_BLOCKER,
                YacorapiTestData::MODE_BLOCKER_ADDON_NAME_NOTEXISTS,
            ],
            'AllMode'             => [
                YacorapiTestData::MODE_ALL_ADDON_NAME_MACRO_COUNT,
                AllAddon::ADDON_ALL,
                YacorapiTestData::MODE_ALL_ADDON_NAME,
            ],
            'AllModeNotExist'     => [
                YacorapiTestData::MODE_ALL_ADDON_NAME_NOTEXIST_MACRO_COUNT,
                AllAddon::ADDON_ALL,
                YacorapiTestData::MODE_ALL_ADDON_NAME_NOTEXIST,
            ],
            'WrongMode'           => [
                0,
                YacorapiTestData::MODE_NOTEXIST,
                YacorapiTestData::MODE_SINGLE_ADDON_NAME,
            ],
        ];
    }

    /**
     * @param int             $expected
     * @param null|int|string $mode
     *
     * @dataProvider providerGetMacro
     */
    public function testGetMacroNamesByMode(int $expected, int|string|null $mode = null): void
    {
        if (is_null($mode)) {
            $actual = $this->getCasto2t()->getMacroNamesByMode();
        } else {
            $actual = $this->getCasto2t()->getMacroNamesByMode($mode);
        }
        self::assertIsArray($actual);
        self::assertCount($expected, $actual);
    }
}
