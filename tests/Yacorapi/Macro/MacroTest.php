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
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\TestCase;

class MacroTest extends TestCase
{
    /**
     * @param string $macroClazz
     * @param int    $expectedAddons
     * @param int    $expectedMacros
     * @param bool   $withConstData
     *
     * @dataProvider providerGetAddonsGetMacros
     */
    public function testMacro(string $macroClazz, int $expectedAddons, int $expectedMacros = -1, bool $withConstData = false): void
    {
        if ($withConstData) {
            /** @var IAddon $newInstance */
            $newInstance = new $macroClazz(new ConstData());
        } else {
            /** @var IAddon $newInstance */
            $newInstance = new $macroClazz();
        }
        $actualAddons = $newInstance->getAddons();
        $actualMacros = $newInstance->getMacros();

        self::assertInstanceOf(Map::class, $actualAddons);
        self::assertCount($expectedAddons, $actualAddons);
        self::assertInstanceOf(Vector::class, $actualMacros);
        self::assertCount($expectedMacros, $actualMacros);
    }

    /**
     * @return array<string,array<int,mixed>>
     */
    public function providerGetAddonsGetMacros(): array
    {
        return [
            'AllAddon'        => [
                YacorapiTestData::CLAZZ_ALL_ADDON,
                YacorapiTestData::MODE_ALL_ADDON_COUNT_TOTAL,
                YacorapiTestData::MODE_ALL_MACRO_COUNT_TOTAL,
                true
            ],
            'BlockerAddon'    => [
                YacorapiTestData::CLAZZ_BLOCKER_ADDON,
                YacorapiTestData::MODE_BLOCKER_ADDON_COUNT_TOTAL,
                YacorapiTestData::MODE_BLOCKER_MACRO_COUNT_TOTAL
            ],
            'SingleAddon'     => [
                YacorapiTestData::CLAZZ_SINGLE_ADDON,
                YacorapiTestData::MODE_SINGLE_ADDON_COUNT_TOTAL,
                YacorapiTestData::MODE_SINGLE_MACRO_COUNT_TOTAL
            ],
            'AtlassianAddon'  => [
                YacorapiTestData::CLAZZ_ATLASSIAN_ADDON,
                YacorapiTestData::EXT_ATLASSIAN_ADDON,
                YacorapiTestData::EXT_ATLASSIAN_MACRO
            ],
            'ProjectdocAddon' => [
                YacorapiTestData::CLAZZ_PDT,
                YacorapiTestData::EXT_PDT_ADDON,
                YacorapiTestData::EXT_PDT_MACRO
            ],
            'ThirdPartyAddon' => [
                YacorapiTestData::CLAZZ_3PARTY,
                YacorapiTestData::EXT_3PARTY_ADDON,
                YacorapiTestData::EXT_3PARTY_MACRO
            ],
            'UserAddon'       => [
                YacorapiTestData::CLAZZ_USER_MACRO_ADDON,
                YacorapiTestData::EXT_USER_MACRO_ADDON,
                YacorapiTestData::EXT_USER_MACRO_MACRO
            ],
        ];
    }
}
