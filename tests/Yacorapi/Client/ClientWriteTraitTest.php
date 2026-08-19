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

namespace oglow\tools\Yacorapi\Client;

use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class ClientWriteTraitTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): ClientWriteTraitTestClazz
    {
        return new ClientWriteTraitTestClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): ClientWriteTraitTestClazz
    {
        return $this->o2t;
    }

    public function testPrepareUpdateURL(): void
    {
        $pageId = YacorapiTestData::C_PAGEID_EXIST;

        $expected1 = ConstData::C_RAPI_CONTENT;
        $expected2 = "$pageId";

        $actual = $this->getCasto2t()->publicPrepareUpdateURL($pageId);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }

    public function testPrepareCreatePage(): void
    {
        $expected1 = ConstData::C_RAPI_CONTENT;

        $actual = $this->getCasto2t()->publicPrepareCreatePage();

        self::assertStringContainsString($expected1, $actual);
    }
}
