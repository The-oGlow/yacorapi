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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\EasyGoingTestCase;

class AddonMacroDataTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): AddonMacroData
    {
        return new AddonMacroData();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): AddonMacroData
    {
        return $this->o2t;
    }

    /**
     * @param int                $expected
     * @param null|AddonTypeEnum $mode
     */
    #[DataProvider('providerGetMacro')]
    public function testGetMacros(int $expected, AddonTypeEnum|null $mode): void
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
     * @param int                $expected
     * @param null|AddonTypeEnum $mode
     */
    #[DataProvider('providerGetMacro')]
    public function testGetMacroNamesByMode(int $expected, AddonTypeEnum|null $mode): void
    {
        if (is_null($mode)) {
            $actual = $this->getCasto2t()->getMacroNamesByMode();
        } else {
            $actual = $this->getCasto2t()->getMacroNamesByMode($mode);
        }
        self::assertIsArray($actual);
        self::assertCount($expected, $actual);
    }

    /**
     * @param int           $expected
     * @param AddonTypeEnum $mode
     * @param string        $addon
     */
    #[DataProvider('providerGetMacroNamesByAddon')]
    public function testgetMacroNamesByAddon(int $expected, AddonTypeEnum $mode, string $addon): void
    {
        $actual = $this->getCasto2t()->getMacroNamesByAddon($mode, $addon);

        self::assertIsArray($actual);
        self::assertCount($expected, $actual);
    }

    /**
     * @return array<string,array<int,mixed>>
     */
    public static function providerGetMacro(): array
    {
        return [
            'DefaultMode' => [YacorapiTestData::MODE_SINGLE_MACRO_COUNT_TOTAL, null],
            'SingleMode' => [YacorapiTestData::MODE_SINGLE_MACRO_COUNT_TOTAL, AddonTypeEnum::ADDON_SINGLE],
            'BlockerMode' => [YacorapiTestData::MODE_BLOCKER_MACRO_COUNT_TOTAL, AddonTypeEnum::ADDON_BLOCKER],
            'AllMode' => [YacorapiTestData::MODE_ALL_MACRO_COUNT_TOTAL, AddonTypeEnum::ADDON_ALL],
        ];
    }

    /**
     * @return array<string,array<int,mixed>>
     */
    public static function providerGetMacroNamesByAddon(): array
    {
        return [
            'SingleMode'          => [
                YacorapiTestData::MODE_SINGLE_ADDON_NAME_MACRO_COUNT,
                AddonTypeEnum::ADDON_SINGLE,
                YacorapiTestData::MODE_SINGLE_ADDON_NAME,
            ],
            'SingleModeNotExist'  => [
                YacorapiTestData::MODE_SINGLE_ADDON_NAME_NOTEXIST_MACRO_COUNT,
                AddonTypeEnum::ADDON_SINGLE,
                YacorapiTestData::MODE_SINGLE_ADDON_NAME_NOTEXIST,
            ],
            'BlockerMode'         => [
                YacorapiTestData::MODE_BLOCKER_ADDON_NAME_MACRO_COUNT,
                AddonTypeEnum::ADDON_BLOCKER,
                YacorapiTestData::MODE_BLOCKER_ADDON_NAME,
            ],
            'BlockerModeNotExist' => [
                YacorapiTestData::MODE_BLOCKER_ADDON_NAME_NOTEXISTS_MACRO_COUNT,
                AddonTypeEnum::ADDON_BLOCKER,
                YacorapiTestData::MODE_BLOCKER_ADDON_NAME_NOTEXISTS,
            ],
//            'AllMode'             => [
//                YacorapiTestData::MODE_ALL_ADDON_NAME_MACRO_COUNT,
//                AddonTypeEnum::ADDON_ALL,
//                YacorapiTestData::MODE_ALL_ADDON_NAME,
//            ],
            'AllModeNotExist'     => [
                YacorapiTestData::MODE_ALL_ADDON_NAME_NOTEXIST_MACRO_COUNT,
                AddonTypeEnum::ADDON_ALL,
                YacorapiTestData::MODE_ALL_ADDON_NAME_NOTEXIST,
            ],
        ];
    }
}
