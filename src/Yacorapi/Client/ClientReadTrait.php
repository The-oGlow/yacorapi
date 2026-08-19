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
use oglow\tools\Yacorapi\Data\QueryExtensionEnum;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\Data\SpaceTypeEnum;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Macro\AddonTypeEnum;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;

trait ClientReadTrait
{
    /**
     * @inheritDoc
     */
    #[\Override]
    public function readPageByPageId(int $pageId): IResponse
    {
        self::$logger->debug('START - pageId', [$pageId]);

        $prepareUrl = $this->prepareLoadUrl($pageId);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse
    {
        self::$logger->debug('START - filterTerm,spaceKey', [$filterTerm, $spaceKey]);

        $prepareUrl = $this->prepareBrowseUrl($filterTerm, $spaceKey);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function scanPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse
    {
        self::$logger->debug('START - filterTerm,spaceKey', [$filterTerm, $spaceKey]);

        $prepareUrl = $this->prepareScanUrl($filterTerm, $spaceKey);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function searchPagesWithFilter(
        string $filterTerm,
        string $spaceKey,
        int $searchFromPos = IRapiClient::REQ_SEARCH_FROM_POS,
        int $searchLimit = IRapiClient::REQ_SEARCH_LIMIT,
        ItemTypeEnum $itemType = IRapiClient::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug(
            'START - filterTerm,spaceKey,searchFromPos,searchLimit,itemType',
            [$filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType]
        );
        $searchLimit = (int) ($searchLimit < IRapiClient::REQ_SEARCH_LIMIT_1ENTRY ? $this->constData->c(ConstData::KEY_SEARCH_LIMIT) : $searchLimit);
        $prepareUrl = $this->prepareSearchUrlExt($filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function prepareAddonSet(AddonTypeEnum $mode = AddonTypeEnum::ADDON_ALL): ResponseAddonMacroDecorate
    {
        self::$logger->debug('START - mode', [$mode]);

        $data = $this->addons->getDataByMode($mode->value); // @phpstan-ignore method.notFound
        if (!empty($data)) {
            /** @psalm-suppress MixedMethodCall */
            $addonSet = new ResponseAddonMacroDecorate($mode, $data->toArray());
        } else {
            $addonSet = new ResponseAddonMacroDecorate($mode);
        }
        self::$logger->debug('END');

        return $addonSet;
    }

    protected function addSpaceFilter(string $spaceKey, string $prepareUrl): string
    {
        if (!empty($spaceKey)) {
            $prepareUrl .= sprintf('&spaceKey=%s', $spaceKey);
        }

        return $prepareUrl;
    }

    protected function prepareSearchUrl(
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

    protected function prepareSearchUrlExt(
        string $searchTerm,
        string $spaceKey,
        int $searchFromPos = RequestParameterData::NO_SEARCH_START,
        int $searchLimit = RequestParameterData::NO_SEARCH_LIMIT,
        ItemTypeEnum $pageType = ItemTypeEnum::PAGE,
        bool $withBody = RequestParameterData::NO_BODY
    ): string {
        $searchLimit = $searchLimit <= RequestParameterData::SEARCH_LIMIT_ZERO ? $this->constData->c(ConstData::KEY_SEARCH_LIMIT) : $searchLimit;
        $prepareUrl = sprintf('%s?cql=', $this->constData->c(ConstData::KEY_CONF_SEARCH_URL));
        $prepareUrl .= sprintf('siteSearch~%s', urlencode("\"{$searchTerm}\""));
        $prepareUrl .= sprintf('+AND+space.type=%s', urlencode(SpaceTypeEnum::SPACE_TYPE_GLOBAL->value));
        $prepareUrl .= sprintf('+AND+type=%s', urlencode("\"{$pageType->value}\""));
        if (!empty($spaceKey)) {
            $prepareUrl .= sprintf('+AND+space=%s', urlencode("\"{$spaceKey}\""));
        }
        if ($searchFromPos >= RequestParameterData::NO_SEARCH_START) {
            $prepareUrl .= sprintf('&start=%s&limit=%s', $searchFromPos, $searchLimit);
        }
        $prepareUrl .= sprintf('&%s', ($withBody ? QueryExtensionEnum::REQP_SEARCH_FULL->value : QueryExtensionEnum::REQP_SEARCH_LIGHT->value));

        return $prepareUrl;
    }

    protected function prepareBrowseUrl(string $filterTerm, string $spaceKey = RequestParameterData::NO_SPACE): string
    {
        $prepareUrl = sprintf('%s?%s&%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $filterTerm, QueryExtensionEnum::REQP_LIGHT->value);

        return $this->addSpaceFilter($spaceKey, $prepareUrl);
    }

    protected function prepareScanUrl(string $filterTerm, string $spaceKey = RequestParameterData::NO_SPACE): string
    {
        $prepareUrl = sprintf('%s/scan?%s&%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $filterTerm, QueryExtensionEnum::REQP_LIGHT->value);

        return $this->addSpaceFilter($spaceKey, $prepareUrl);
    }

    protected function prepareApiByPageIdUrl(int $pageId): string
    {
        return sprintf('%s/%s?%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $pageId, QueryExtensionEnum::REQP_LIGHT->value);
    }

    protected function prepareLoadUrl(int $pageId): string
    {
        return sprintf('%s/%s?%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $pageId, QueryExtensionEnum::REQP_FULL->value);
    }

    /**
     * @param IResponse $response
     *
     * @return int
     */
    protected function analyzeResponse(IResponse $response): int
    {
        return (int) $response->getValue(IResponse::KEY_TOTAL_SIZE, 0);
    }
}
