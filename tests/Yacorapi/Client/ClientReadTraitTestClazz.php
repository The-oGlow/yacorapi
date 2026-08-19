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
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;

class ClientReadTraitTestClazz implements IRapiClientRead
{
    use ClientReadTrait;

    protected ConstData $constData;

    public function __construct()
    {
        $this->constData = new ConstData(ClientReadTraitTestClazz::class);
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
        string $spaceKey = RequestParameterData::NO_SPACE,
        ItemTypeEnum $pageType = ItemTypeEnum::PAGE,
        bool $withBody = RequestParameterData::NO_BODY
    ): string {
        return $this->prepareSearchUrl($searchTerm, $spaceKey, $pageType, $withBody);
    }

    public function publicPrepareSearchUrlExt(
        string $searchTerm,
        string $spaceKey,
        int $searchFromPos = RequestParameterData::NO_SEARCH_START,
        int $searchLimit = RequestParameterData::NO_SEARCH_LIMIT,
        ItemTypeEnum $pageType = ItemTypeEnum::PAGE,
        bool $withBody = RequestParameterData::NO_BODY
    ): string {
        return $this->prepareSearchUrlExt($searchTerm, $spaceKey, $searchFromPos, $searchLimit, $pageType, $withBody);
    }

    public function publicPrepareBrowseUrl(string $filterTerm, string $spaceKey = RequestParameterData::NO_SPACE): string
    {
        return $this->prepareBrowseUrl($filterTerm, $spaceKey);
    }

    public function publicPrepareScanUrl(string $filterTerm, string $spaceKey = RequestParameterData::NO_SPACE): string
    {
        return $this->prepareScanUrl($filterTerm, $spaceKey);
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
