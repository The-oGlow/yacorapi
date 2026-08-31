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

use Ds\Set;
use Monolog\ConsoleLogger;
use oglow\tools\common\MockProvider;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Response\Response;
use oglow\tools\Yacorapi\Statistic\StatisticStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticTypeEnum;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class RapiClientTest extends EasyGoingTestCase
{
    public const array AVAILABLE_METHODS = [
        'newClient', 'readPageByPageId', 'readPagesByTitle', 'checkPageExists', 'scanPages', 'searchPagesWithFilter', 'countItemsinSpace', 'spaceHomepage',
        'readRestrictionsByPageId', 'writeRestrictionsByPageId', 'listSpaces', 'countMacrosInSpace', 'createPage', 'updatePage', 'createOrUpdatePage',
             'movePage', 'prepareAddonSet', 'taskitemMethods', 'getExtensionAddonMacros', 'processQueue',
    ];

    /** Space on test instance */
    public const string SPACE_KEY = 'NMAS';

    /** Page id of space on test instance */
    public const int SPACE_HOMEPAGE_ID = 125380876;

    public const int PLAYGROUND_PAGEID = 532951146;

    public const string PAGE_TITLE = 'NEW PAGE %s-%s';

    public const string PAGE_BODY = "<p>This is <br/> a new page</p>\n";

    public const string SEARCH_TERM = 'title=REST-API%2001';

    private static LoggerInterface $logger;

    #[\Override]
    public function setUp(): void
    {
        self::$logger = new ConsoleLogger(RapiClientTest::class);
        self::$logger->debug('START');
        parent::setUp();
        self::$logger->debug('END');
    }

    #[\Override]
    protected static function prepareO2t(): IRapiClient
    {
        return RapiClient::newClient(connectionProvider: new MockProvider(LogLevel::DEBUG));
    }

    #[\Override]
    protected function getCasto2t(): IRapiClient
    {
        return $this->o2t;
    }

    public function testRapiMethods(): void
    {
        $expected = new Set(self::AVAILABLE_METHODS);

        /** @var Set<non-empty-string> */
        $actual = $this->getCasto2t()::taskitemMethods();

        self::assertInstanceOf(Set::class, $actual);
        foreach ($actual->getIterator() as $item) {
            if ($expected->contains($item)) {
                $expected->remove($item);
            } else {
                $expected->add($item);
            }
        }
        self::assertTrue($expected->isEmpty(), sprintf("Forgotten: '%s'", join('()\',\'', $expected->toArray())));
    }

    public function testCreatePage(): void
    {
        self::$logger->info('START');

        $newSpaceKey = self::SPACE_KEY;
        $newTitle = sprintf(self::PAGE_TITLE, date('Ymd-His'), 1);
        $newBody = self::PAGE_BODY;
        $newParent = self::SPACE_HOMEPAGE_ID;

        $response = $this->getCasto2t()->createPage($newSpaceKey, $newTitle, $newBody, $newParent);

        self::$logger->info('response', [$response->getResponse()]);

        self::assertNotEmpty($response);
        self::assertNotEmpty($response->getValue(IResponse::KEY_ID));

        self::assertEquals($newTitle, $response->getValue(IResponse::KEY_TITLE));
        self::assertEquals($newBody, $response->getBody());
        self::assertEquals($newSpaceKey, $response->getValue(IResponse::KEY_SPACE)[IResponse::KEY_KEY]);
        self::assertEquals($newParent, $response->getValue(IResponse::KEY_ANCESTORS)[IResponse::KEY_ID]);

        self::$logger->info('END');
    }

    public function testUpdatePage(): void
    {
        self::$logger->info('START');

        $updateId = YacorapiTestData::C_SEARCHPAGEID_01;
        $updateTitle = sprintf(self::PAGE_TITLE, date('Ymd-His'), 1);
        $updateBody = self::PAGE_BODY;

        $before = $this->getCasto2t()->readPageByPageId($updateId);
        self::$logger->info('before', [$before->getResponse()]);

        self::assertNotEmpty($before);
        self::assertEquals($updateId, $before->getValue(IResponse::KEY_ID));

        $after = $this->getCasto2t()->updatePage($updateId, $updateBody, $updateTitle);
        self::$logger->info('after', [$after->getResponse()]);

        self::assertNotEmpty($after);
        self::assertEquals($updateId, $after->getValue(IResponse::KEY_ID));
        self::assertEquals($updateTitle, $after->getValue(IResponse::KEY_TITLE));
        self::assertEquals($updateBody, $after->getBody());

        self::assertNotEquals($before->getValue(IResponse::KEY_TITLE), $after->getValue(IResponse::KEY_TITLE));
        self::assertNotEquals($before->getBody(), $after->getBody());

        self::$logger->info('END');
    }

    public function testMovePage(): void
    {
        self::$logger->info('START');

        $response = $this->getCasto2t()->movePage(YacorapiTestData::C_SEARCHPAGEID_01, YacorapiTestData::C_PAGEID_NEW);

        self::$logger->info('response', [$response->getResponse()]);
        self::assertNotEmpty($response);

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

    public function testReadRestrictionsByPageId(): void
    {
        self::$logger->info('START');

        $response = $this->getCasto2t()->readRestrictionsByPageId(YacorapiTestData::C_SEARCHPAGEID_01);

        self::$logger->info('response', [$response->getResponse()]);
        self::assertNotEmpty($response);
        self::assertNotEmpty($response->getRestrictions());

        self::$logger->info('END');
    }

    public function testWriteRestrictionsByPageId(): void
    {
        self::$logger->info('START');

        $expected = true;

        $success = $this->getCasto2t()->writeRestrictionsByPageId(YacorapiTestData::C_SEARCHPAGEID_01);

        self::assertEquals($expected, $success);

        self::$logger->info('END');
    }

    public function testListSpaces(): void
    {
        self::$logger->info('START');

        $expectedCount = 1;

        $response = $this->getCasto2t()->listSpaces();

        $actualCount = $response->getResponse()->get(Response::KEY_TOTAL_SIZE, -1);

        self::$logger->info('response', [$response->getResponse()]);
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
}
