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
use oglow\tools\Yacorapi\Response\Response;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class ClientReadTraitTest extends EasyGoingTestCase
{
    private const string C_FILTER_SPACEKEY = '&spaceKey=';

    #[\Override]
    protected static function prepareO2t(): ClientReadTraitTestClazz
    {
        return new ClientReadTraitTestClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): ClientReadTraitTestClazz
    {
        return $this->o2t;
    }

    public function testAnalyzeResponseEmpty(): void
    {
        $response = new Response();

        $expected = YacorapiTestData::C_RESPONSE_SIZE_EMPTY;
        $actual   = $this->getCasto2t()->publicAnalyzeResponse($response);

        self::assertEquals($expected, $actual);
    }

    public function testAddSpaceFilterEmptyBoth(): void
    {
        $spaceKey   = YacorapiTestData::C_SPACE_EMPTY;
        $prepareUrl = YacorapiTestData::C_SEARCHTERM_EMPTY;

        $actual = $this->getCasto2t()->publicAddSpaceFilter($spaceKey, $prepareUrl);

        self::assertEmpty($actual);
    }

    public function testAddSpaceFilterEmptyUrl(): void
    {
        $spaceKey   = YacorapiTestData::C_SPACE_EXIST_KEY;
        $prepareUrl = YacorapiTestData::C_SEARCHTERM_EMPTY;

        $expected = self::C_FILTER_SPACEKEY . $spaceKey;

        $actual = $this->getCasto2t()->publicAddSpaceFilter($spaceKey, $prepareUrl);

        self::assertEquals($expected, $actual);
    }

    public function testAddSpaceFilterFilledBoth(): void
    {
        $spaceKey   = YacorapiTestData::C_SPACE_EXIST_KEY;
        $prepareUrl = YacorapiTestData::C_PREPURL_01;

        $expected = $prepareUrl . self::C_FILTER_SPACEKEY . $spaceKey;

        $actual = $this->getCasto2t()->publicAddSpaceFilter($spaceKey, $prepareUrl);

        self::assertEquals($expected, $actual);
    }

    public function testPrepareSearchUrl(): void
    {
        $searchTerm = YacorapiTestData::C_SEARCHTERM_01;

        $expected1 = ConstData::C_RAPI_CONTENT;
        $expected2 = YacorapiTestData::C_SPACE_EMPTY;

        $actual = $this->getCasto2t()->publicPrepareSearchUrl($searchTerm);

        self::assertStringNotContainsString($expected1, $actual);
        self::assertEquals($expected2, $actual);
    }

    public function testPrepareSearchUrlExt(): void
    {
        $searchTerm = YacorapiTestData::C_SEARCHTERM_01;
        $spaceKey   = YacorapiTestData::C_SPACE_EXIST_KEY;

        $expected1 = ConstData::C_RAPI_SEARCH;
        $expected2 = $searchTerm;
        $expected3 = $spaceKey;

        $actual = $this->getCasto2t()->publicPrepareSearchUrlExt($searchTerm, $spaceKey);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
        self::assertStringContainsString($expected3, $actual);
    }

    public function testPrepareBrowseUrl(): void
    {
        $filterTerm = YacorapiTestData::C_FILTERTERM_01;
        $spaceKey   = YacorapiTestData::C_SPACE_EXIST_KEY;

        $expected1 = ConstData::C_RAPI_CONTENT;
        $expected2 = $filterTerm;
        $expected3 = $spaceKey;

        $actual = $this->getCasto2t()->publicPrepareBrowseUrl($filterTerm, $spaceKey);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
        self::assertStringContainsString($expected3, $actual);
    }

    public function testPrepareScanUrl(): void
    {
        $filterTerm = YacorapiTestData::C_FILTERTERM_01;
        $spaceKey   = YacorapiTestData::C_SPACE_EXIST_KEY;

        $expected1 = $filterTerm;
        $expected2 = $spaceKey;

        $actual = $this->getCasto2t()->publicPrepareScanUrl($filterTerm, $spaceKey);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }

    public function testPrepareApiByPageIdUrl(): void
    {
        $pageId = YacorapiTestData::C_PAGEID_EXIST;

        $expected1 = ConstData::C_RAPI_CONTENT;
        $expected2 = "$pageId";

        $actual = $this->getCasto2t()->publicPrepareApiByPageIdUrl($pageId);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }

    public function testPrepareLoadUrl(): void
    {
        $pageId = YacorapiTestData::C_PAGEID_EXIST;

        $expected1 = ConstData::C_RAPI_CONTENT;
        $expected2 = "$pageId";

        $actual = $this->getCasto2t()->publicPrepareLoadUrl($pageId);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }
}
