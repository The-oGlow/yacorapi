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

namespace oglow\tools\Yacorapi\Extension;

use Ds\Map;
use Ds\Vector;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class AbstractExtensionTest extends EasyGoingTestCase
{
    /**
     * @return AbstractExtensionTestClazz
     */
    protected static function prepareO2t(): AbstractExtensionTestClazz
    {
        return new AbstractExtensionTestClazz();
    }

    /**
     * @return AbstractExtensionTestClazz
     */
    protected function getCasto2t(): AbstractExtensionTestClazz
    {
        return $this->o2t;
    }

    public function testGetName(): void
    {
        $expected = YacorapiTestData::NOTEXIST_NAME;

        $actual = $this->getCasto2t()::getName();

        self::assertEquals($expected, $actual);
    }

    public function testGetId(): void
    {
        $expected = YacorapiTestData::NOTEXIST_ID;

        $actual = $this->getCasto2t()::getId();

        self::assertEquals($expected, $actual);
    }

    public function testGetAddons(): void
    {
        $expected = new Map([YacorapiTestData::ADDON_1 => new Vector(YacorapiTestData::ADDON_1_ORDER)]);

        $actual = $this->getCasto2t()->getAddons();

        self::assertEquals($expected, $actual);
    }

    public function testGetMacros(): void
    {
        $expected = new Vector(YacorapiTestData::ADDON_1_ORDER);

        $actual = $this->getCasto2t()->getMacros();

        self::assertEquals($expected, $actual);
    }
}
