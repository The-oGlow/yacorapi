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
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Space\SpaceTypeEnum;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;

class ClientStatisticTraitTest extends EasyGoingTestCase
{
    private static LoggerInterface $logger; // @phpstan-ignore property.onlyWritten

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$logger = new ConsoleLogger(ClientReadTraitTest::class);
    }

    #[\Override]
    protected static function prepareO2t(): ClientStatisticTraitTestClazz
    {
        return new ClientStatisticTraitTestClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): ClientStatisticTraitTestClazz
    {
        return $this->o2t;
    }

    public function testPrepareSpacePagesUrl(): void
    {
        $spaceKey = YacorapiTestData::C_SPACE_EXIST_KEY;

        $expected1 = ConstData::C_RAPI_SPACE;
        $expected2 = $spaceKey;

        $actual   = $this->getCasto2t()->publicPrepareSpacePagesUrl($spaceKey);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }

    public function testPrepareSpaceListUrl(): void
    {
        $expected1 = ConstData::C_RAPI_SPACE;
        $expected2 = SpaceTypeEnum::SPACE_TYPE_GLOBAL->value;
        $expected3 = '' . ConstData::PAGE_LIMIT;

        $actual    = $this->getCasto2t()->publicPrepareSpaceListUrl();

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
        self::assertStringContainsString($expected3, $actual);
    }

    public function testPrepareCountItemsUrl(): void
    {
        $filterTerm = ItemTypeEnum::PAGE;
        $spaceKey = YacorapiTestData::C_SPACE_EXIST_KEY;

        $expected1 = $filterTerm;
        $expected2 = $spaceKey;

        $actual = $this->getCasto2t()->publicPrepareCountItemsUrl($filterTerm, $spaceKey);

        self::assertStringContainsString($expected1->value, $actual);
        self::assertStringContainsString($expected2, $actual);
    }
}
