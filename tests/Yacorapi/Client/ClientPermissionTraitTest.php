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

use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;

class ClientPermissionTraitTest extends EasyGoingTestCase
{
    private static LoggerInterface $logger; // @phpstan-ignore property.onlyWritten

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$logger = new ConsoleLogger(ClientReadTraitTest::class);
    }

    #[\Override]
    protected static function prepareO2t(): ClientPermissionTraitTestClazz
    {
        return new ClientPermissionTraitTestClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): ClientPermissionTraitTestClazz
    {
        return $this->o2t;
    }

    public function testPrepareRestrictByOpUrl(): void
    {
        $pageId = YacorapiTestData::C_PAGEID_EXIST;

        $expected1 = ConstData::C_RAPI_RESTRICTION_BYOP;
        $expected2 = "$pageId";

        $actual = $this->getCasto2t()->publicPrepareRestrictByOpUrl($pageId);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }

    public function testPrepareRestrictUpdateUrl(): void
    {
        $pageId = YacorapiTestData::C_PAGEID_EXIST;

        $expected1 = ConstData::C_RAPI_RESTRICTION;
        $expected2 = "$pageId";

        $actual = $this->getCasto2t()->publicPrepareRestrictUpdateUrl($pageId);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }

    public function testWriteRestrictionsByPageId(): void
    {
        $pageId = YacorapiTestData::C_PAGEID_EXIST;

        $expected1 = true;

        $actual = $this->getCasto2t()->publicWriteRestrictionsByPageId($pageId);

        self::assertEquals($expected1, $actual);
    }

    public function testAddRestrictionForGroupEmpty(): void
    {
        $restrictions = [];

        $expected1 = [RequestParameterData::PROP_GROUP => []];

        $actual       = $this->getCasto2t()->publicAddRestrictionForGroup($restrictions);

        self::assertEquals($expected1, $actual);
    }

    public function testAddRestrictionForUserEmpty(): void
    {
        $restrictions = [];

        $expected1 = [RequestParameterData::PROP_USER => []];

        $actual       = $this->getCasto2t()->publicAddRestrictionForUser($restrictions);

        self::assertEquals($expected1, $actual);
    }
}
