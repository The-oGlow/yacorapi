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
use oglow\tools\Yacorapi\Response\ResponseParameterData;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;

class RapiClientWriteTest extends EasyGoingTestCase
{
    private static LoggerInterface $logger;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$logger = new ConsoleLogger(RapiClientWriteTest::class);
    }

    #[\Override]
    protected static function prepareO2t(): RapiClientWriteTestClazz
    {
        return new RapiClientWriteTestClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): RapiClientWriteTestClazz
    {
        return $this->o2t;
    }

    public function testCreatePage(): void
    {
        self::$logger->info('START');

        $spaceKey = YacorapiTestData::C_SPACE_EXIST_KEY;
        $pageTitle = sprintf('%s %s-%s', YacorapiTestData::C_PAGE_TITLE_1, date('Ymd-His'), 1);
        $pageBody = YacorapiTestData::C_PAGE_BODY_1;
        $parentId = YacorapiTestData::C_SPACE_EXIST_ID;

        $response = $this->getCasto2t()->createPage($spaceKey, $pageTitle, $pageBody, $parentId);

        self::$logger->info('response', [$response->getRawData()]);

        self::assertNotEmpty($response);
        self::assertNotEmpty($response->getValue(ResponseParameterData::KEY_ID));

        self::assertEquals($pageTitle, $response->getValue(ResponseParameterData::KEY_TITLE));
        self::assertEquals($pageBody, $response->getBody());
        self::assertEquals($spaceKey, $response->getValue(ResponseParameterData::KEY_SPACE)[ResponseParameterData::KEY_KEY]);
        self::assertEquals($parentId, $response->getValue(ResponseParameterData::KEY_ANCESTORS)[ResponseParameterData::KEY_ID]);

        self::$logger->info('END');
    }

    public function testUpdatePage(): void
    {
        self::$logger->info('START');

        $updateId = YacorapiTestData::C_SEARCHPAGEID_01;
        $updateTitle = sprintf('%s %s-%s', YacorapiTestData::C_PAGE_TITLE_2, date('Ymd-His'), 1);
        $updateBody = YacorapiTestData::C_PAGE_BODY_2;

        $before = $this->getCasto2t()->readPageByPageId($updateId);
        self::$logger->info('before', [$before->getRawData()]);

        self::assertNotEmpty($before);
        self::assertEquals($updateId, $before->getValue(ResponseParameterData::KEY_ID));

        $after = $this->getCasto2t()->updatePage($updateId, $updateBody, $updateTitle);
        self::$logger->info('after', [$after->getRawData()]);

        self::assertNotEmpty($after);
        self::assertEquals($updateId, $after->getValue(ResponseParameterData::KEY_ID));
        self::assertEquals($updateTitle, $after->getValue(ResponseParameterData::KEY_TITLE));
        self::assertEquals($updateBody, $after->getBody());

        self::assertNotEquals($before->getValue(ResponseParameterData::KEY_TITLE), $after->getValue(ResponseParameterData::KEY_TITLE));
        self::assertNotEquals($before->getBody(), $after->getBody());

        self::$logger->info('END');
    }

    public function testMovePage(): void
    {
        self::$logger->info('START');

        $response = $this->getCasto2t()->movePage(YacorapiTestData::C_SEARCHPAGEID_01, YacorapiTestData::C_PAGEID_NEW);

        self::$logger->info('response', [$response->getRawData()]);
        self::assertNotEmpty($response);

        self::$logger->info('END');
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
