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
use oglow\tools\Yacorapi\Response\ResponseParameterData;
use oglow\tools\Yacorapi\Space\SpaceTypeEnum;
use oglow\tools\Yacorapi\Statistic\StatisticStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticTypeEnum;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;

class RapiClientStatisticTest extends EasyGoingTestCase
{
    private static LoggerInterface $logger;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$logger = new ConsoleLogger(RapiClientReadTest::class);
    }

    #[\Override]
    protected static function prepareO2t(): RapiClientStatisticTestClazz
    {
        return new RapiClientStatisticTestClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): RapiClientStatisticTestClazz
    {
        return $this->o2t;
    }

    public function testListSpaces(): void
    {
        self::$logger->info('START');

        $expectedCount = 1;

        $response = $this->getCasto2t()->listSpaces();

        $actualCount = $response->getRawData()->get(ResponseParameterData::KEY_TOTAL_SIZE, -1);

        self::$logger->info('response', [$response->getRawData()]);
        self::$logger->info('results', [$response->getResults()]);

        self::assertNotEmpty($response);
        self::assertCount($expectedCount, $response->getResults());
        self::assertCount($actualCount, $response->getResults());

        self::$logger->info('END');
    }

    public function testCountMacrosInSpace(): void
    {
        self::$logger->info('START');

        $spaceKey = YacorapiTestData::C_SPACE_EXIST_KEY;

        $outputMatrix = new StatisticStatistic($spaceKey, StatisticTypeEnum::SPACE);

        $statistic = $this->getCasto2t()->countMacrosInSpace($spaceKey, $this->getCasto2t()->prepareAddonSet(), $outputMatrix);

        self::$logger->info('statistic', [$statistic]);
        self::assertNotEmpty($statistic);

        self::$logger->info('outputMatrix', [$outputMatrix]);
        self::assertNotEmpty($outputMatrix);

        self::$logger->info('END');
    }

    public function testCountItemsinSpace(): void
    {
        self::$logger->info('START');

        $statistic = $this->getCasto2t()->countItemsinSpace(YacorapiTestData::C_SPACE_EXIST_KEY);

        self::$logger->info('statistic', [$statistic->flatten()]);
        self::assertNotEmpty($statistic);

        self::$logger->info('END');
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
