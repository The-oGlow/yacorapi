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

use PHPUnit\Framework\EasyGoingTestCase;

class RapiClientExtensionTest extends EasyGoingTestCase
{
    /**
     * @return RapiClientExtension
     */
    protected static function prepareO2t(): RapiClientExtension
    {
        return new RapiClientExtension();
    }

    /**
     * @return RapiClientExtension
     */
    protected function getCasto2t(): RapiClientExtension
    {
        return $this->o2t;
    }

    public function testGetName(): void
    {
        $actual = $this->getCasto2t()::getName();

        self::assertNotEmpty($actual);
    }

    public function testGetId(): void
    {
        $expected = 0;

        $actual = $this->getCasto2t()::getId();

        self::assertGreaterThan($expected, $actual);
    }
}
