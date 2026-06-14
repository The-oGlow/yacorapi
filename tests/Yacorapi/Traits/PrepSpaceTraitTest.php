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

namespace oglow\tools\Yacorapi\Traits;

use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class PrepSpaceTraitTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): PrepSpaceTraitTestClazz
    {
        return new PrepSpaceTraitTestClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): PrepSpaceTraitTestClazz
    {
        return $this->o2t;
    }

    public function testPrepareSpacePagesUrl(): void
    {
        $spaceKey = YacorapiTestData::C_SPACE_EXIST_KEY;

        $expected1 = ConstData::C_RAPI_SPACE;
        $expected2 = $spaceKey;

        $actual   = $this->getCasto2t()->prepareSpacePagesUrl($spaceKey);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }

    public function testPrepareSpaceListUrl(): void
    {
        $expected1 = ConstData::C_RAPI_SPACE;
        $expected2 = RequestParameterData::SPACE_TYPE_GLOBAL;
        $expected3 = '' . ConstData::PAGE_LIMIT;

        $actual    = $this->getCasto2t()->prepareSpaceListUrl();

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
        self::assertStringContainsString($expected3, $actual);
    }
}
