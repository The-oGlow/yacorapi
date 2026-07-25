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
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\QueryExtensionEnum;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\Data\SpaceTypeEnum;
use oglow\tools\Yacorapi\IResponse;

trait PrepReadTrait
{
    public function addSpaceFilter(string $spaceKey, string $prepareUrl): string
    {
        if (!empty($spaceKey)) {
            $prepareUrl .= sprintf('&spaceKey=%s', $spaceKey);
        }

        return $prepareUrl;
    }

    public function prepareSearchUrl(
        string $searchTerm,
        string $spaceKey = RequestParameterData::NO_SPACE,
        ItemTypeEnum $pageType = ItemTypeEnum::PAGE,
        bool $withBody = RequestParameterData::NO_BODY
    ): string {
        $result = '';
        if (function_exists('_prepareSearchUrl')) {
            $result = _prepareSearchUrl($searchTerm, $spaceKey, null, null, $pageType, $withBody);
        }

        return $result;
    }

    public function prepareSearchUrlExt(
        string $searchTerm,
        string $spaceKey,
        int $searchFromPos = RequestParameterData::NO_SEARCH_START,
        int $searchLimit = RequestParameterData::NO_SEARCH_LIMIT,
        ItemTypeEnum $pageType = ItemTypeEnum::PAGE,
        bool $withBody = RequestParameterData::NO_BODY
    ): string {
        $searchLimit = $searchLimit <= RequestParameterData::SEARCH_LIMIT_ZERO ? $this->constData->c(ConstData::KEY_SEARCH_LIMIT) : $searchLimit;
        $prepareUrl  = sprintf('%s?cql=', $this->constData->c(ConstData::KEY_CONF_SEARCH_URL));
        $prepareUrl  .= sprintf('siteSearch~%s', urlencode("\"{$searchTerm}\""));
        $prepareUrl  .= sprintf('+AND+space.type=%s', urlencode(SpaceTypeEnum::SPACE_TYPE_GLOBAL->value));
        $prepareUrl  .= sprintf('+AND+type=%s', urlencode("\"{$pageType->value}\""));
        if (!empty($spaceKey)) {
            $prepareUrl .= sprintf('+AND+space=%s', urlencode("\"{$spaceKey}\""));
        }
        if ($searchFromPos >= RequestParameterData::NO_SEARCH_START) {
            $prepareUrl .= sprintf('&start=%s&limit=%s', $searchFromPos, $searchLimit);
        }
        $prepareUrl .= sprintf('&%s', ($withBody ? QueryExtensionEnum::REQP_SEARCH_FULL->value : QueryExtensionEnum::REQP_SEARCH_LIGHT->value));

        return $prepareUrl;
    }

    public function prepareBrowseUrl(string $filterTerm, string $spaceKey = RequestParameterData::NO_SPACE): string
    {
        $prepareUrl = sprintf('%s?%s&%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $filterTerm, QueryExtensionEnum::REQP_LIGHT->value);

        return $this->addSpaceFilter($spaceKey, $prepareUrl);
    }

    public function prepareScanUrl(string $filterTerm, string $spaceKey = RequestParameterData::NO_SPACE): string
    {
        $prepareUrl = sprintf('%s/scan?%s&%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $filterTerm, QueryExtensionEnum::REQP_LIGHT->value);

        return $this->addSpaceFilter($spaceKey, $prepareUrl);
    }

    public function prepareApiByPageIdUrl(int $pageId): string
    {
        return sprintf('%s/%s?%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $pageId, QueryExtensionEnum::REQP_LIGHT->value);
    }

    public function prepareLoadUrl(int $pageId): string
    {
        return sprintf('%s/%s?%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $pageId, QueryExtensionEnum::REQP_FULL->value);
    }

    public function prepareCountItemsUrl(ItemTypeEnum $itemType, string $spaceKey): string
    {
        return ((string)$this->constData->c(ConstData::KEY_CONF_SEARCH_URL)) . "?cql=type+in+(" . $itemType->value . ")+AND+space=$spaceKey";
    }

    /**
     * @param IResponse $response
     *
     * @return int
     */
    public function analyzeResponse(IResponse $response): int
    {
        return (int)$response->getValue(IResponse::KEY_TOTAL_SIZE, 0);
    }
}
