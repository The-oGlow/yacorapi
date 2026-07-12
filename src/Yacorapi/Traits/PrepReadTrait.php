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
use oglow\tools\Yacorapi\Data\RequestParameterData;
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

    public function prepareSearchUrl(string $searchTerm, string $spaceKey = '', string $pageType = 'page', bool $withBody = false): string
    {
        $result='';
        if (function_exists('_prepareSearchUrl')) {
            $result= _prepareSearchUrl($searchTerm, $spaceKey, null, null, $pageType, $withBody);
        }
        return $result;
    }

    public function prepareSearchUrlExt(
        string $searchTerm,
        string $spaceKey,
        int $searchFromPos = 0,
        int $searchLimit = -1,
        string $pageType = 'page',
        bool $withBody = false
    ): string {
        $searchLimit = $searchLimit <= 0 ? $this->constData->c(ConstData::KEY_SEARCH_LIMIT) : $searchLimit;
        $prepareUrl  = sprintf('%s?cql=', $this->constData->c(ConstData::KEY_CONF_SEARCH_URL));
        $prepareUrl  .= sprintf('siteSearch~%s', urlencode("\"{$searchTerm}\""));
        $prepareUrl  .= sprintf('+AND+space.type=%s', urlencode('global'));
        $prepareUrl  .= sprintf('+AND+type=%s', urlencode("\"{$pageType}\""));
        if (!empty($spaceKey)) {
            $prepareUrl .= sprintf('+AND+space=%s', urlencode("\"{$spaceKey}\""));
        }
        if (!is_null($searchFromPos)) {
            $prepareUrl .= sprintf('&start=%s&limit=%s', $searchFromPos, $searchLimit);
        }
        $prepareUrl .= sprintf('&%s', ($withBody ? RequestParameterData::REQP_SEARCH_FULL : RequestParameterData::REQP_SEARCH_LIGHT));

        return $prepareUrl;
    }

    public function prepareBrowseUrl(string $filterTerm, string $spaceKey = ''): string
    {
        $prepareUrl = sprintf('%s?%s&%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $filterTerm, RequestParameterData::REQP_LIGHT);

        return $this->addSpaceFilter($spaceKey, $prepareUrl);
    }

    public function prepareScanUrl(string $filterTerm, string $spaceKey = ''): string
    {
        $prepareUrl = sprintf('%s/scan?%s&%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $filterTerm, RequestParameterData::REQP_LIGHT);

        return $this->addSpaceFilter($spaceKey, $prepareUrl);
    }

    public function prepareApiByPageIdUrl(int $pageId): string
    {
        return sprintf('%s/%s?%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $pageId, RequestParameterData::REQP_LIGHT);
    }

    public function prepareLoadUrl(int $pageId): string
    {
        return sprintf('%s/%s?%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $pageId, RequestParameterData::REQP_FULL);
    }

    public function prepareCountItemsUrl(string $pageType, string $spaceKey): string
    {
        return ((string)$this->constData->c(ConstData::KEY_CONF_SEARCH_URL)) . "?cql=type+in+($pageType)+AND+space=$spaceKey";
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
