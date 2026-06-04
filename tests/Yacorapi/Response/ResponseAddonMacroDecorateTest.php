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

use oglow\tools\Yacorapi\Macro\SingleAddon;
use PHPUnit\Framework\EasyGoingTestCase;

class ResponseAddonMacroDecorateTest extends EasyGoingTestCase
{
    /**
     * @return ResponseAddonMacroDecorate
     */
    protected static function prepareO2t(): ResponseAddonMacroDecorate
    {
        return new ResponseAddonMacroDecorate(SingleAddon::ADDON_SINGLE);
    }

    /**
     * @return ResponseAddonMacroDecorate
     */
    protected function getCasto2t(): ResponseAddonMacroDecorate
    {
        return $this->o2t;
    }

    public function testGetResult(): void
    {
        static::expectException(\BadFunctionCallException::class);
        $this->getCasto2t()->getResult(0);
    }

    public function testGetMode(): void
    {
        $expected = SingleAddon::ADDON_SINGLE;
        $actual = $this->getCasto2t()->getMode();

        self::assertEquals($expected, $actual);
    }
}
