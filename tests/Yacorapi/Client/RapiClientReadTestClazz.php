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
use oglow\tools\common\MockProvider;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Request\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;
use Psr\Log\LoggerInterface;

class RapiClientReadTestClazz extends RapiClientRead implements IRapiClientRead
{
    private static LoggerInterface $logger;

    protected ConstData $constData;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(name: RapiClientReadTestClazz::class, level: self::LEVEL_DEFAULT);
        self::$logger->debug('START');

        parent::__construct(connectionProvider: new MockProvider());

        $this->constData = new ConstData(RapiClientReadTestClazz::class);

        self::$logger->debug('END');
    }

    public function publicAnalyzeResponse(IResponse $response): int
    {
        return $this->analyzeResponse($response);
    }

    public function publicAddSpaceFilter(string $spaceKey, string $prepareUrl): string
    {
        return $this->addSpaceFilter($spaceKey, $prepareUrl);
    }

    public function publicPrepareSearchUrl(
        string $searchTerm,
        string $spaceKey = RequestParameterData::VAL_SPACE_EMPTY,
        ItemTypeEnum $pageType = ItemTypeEnum::PAGE,
        bool $withBody = RequestParameterData::VAL_BODY_NO
    ): string {
        return $this->prepareSearchUrl($searchTerm, $spaceKey, $pageType, $withBody);
    }

    public function publicPrepareSearchUrlExt(
        string $searchTerm,
        string $spaceKey,
        int $searchFromPos = RequestParameterData::VAL_SEARCH_START_NO,
        int $searchLimit = RequestParameterData::VAL_SEARCH_LIMIT_NO,
        ItemTypeEnum $pageType = ItemTypeEnum::PAGE,
        bool $withBody = RequestParameterData::VAL_BODY_NO
    ): string {
        return $this->prepareSearchUrlExt($searchTerm, $spaceKey, $searchFromPos, $searchLimit, $pageType, $withBody);
    }

    public function publicPrepareBrowseUrl(string $filterTerm, string $spaceKey = RequestParameterData::VAL_SPACE_EMPTY): string
    {
        return $this->prepareBrowseUrl($filterTerm, $spaceKey);
    }

    public function publicPrepareScanUrl(string $spaceKey = RequestParameterData::VAL_SPACE_EMPTY): string
    {
        return $this->prepareScanUrl($spaceKey);
    }

    public function publicPrepareApiByPageIdUrl(int $pageId): string
    {
        return $this->prepareApiByPageIdUrl($pageId);
    }

    public function publicPrepareLoadUrl(int $pageId): string
    {
        return $this->prepareLoadUrl($pageId);
    }
}
