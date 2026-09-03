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
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\QueryExtensionEnum;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Macro\AddonTypeEnum;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use oglow\tools\Yacorapi\Space\SpaceTypeEnum;
use Psr\Log\LoggerInterface;

class RapiClientRead extends RapiClientBase implements IRapiClientRead
{
    private static LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param null|ExtensionEnum              $modeExtension      (Default: {@link IRapiClientBase::EXTENSION_DEFAULT})
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|IContainer                 $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link IRapiClientBase::LEVEL_DEFAULT})
     */
    protected function __construct(
        ?ExtensionEnum $modeExtension = IRapiClientBase::EXTENSION_DEFAULT,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ) {
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name: RapiClientRead::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($modeExtension, $connectionProvider, $addons, $level);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function prepareAddonSet(AddonTypeEnum $addonMode = IRapiClientBase::ADDON_DEFAULT): ResponseAddonMacroDecorate
    {
        self::$logger->debug('START - mode', [$addonMode]);

        $data = $this->addons->getDataByMode($addonMode->value);
        if (!empty($data)) {
            /** @psalm-suppress MixedMethodCall */
            $addonSet = new ResponseAddonMacroDecorate($addonMode, $data->toArray());
        } else {
            $addonSet = new ResponseAddonMacroDecorate($addonMode);
        }
        self::$logger->debug('END');

        return $addonSet;
    }

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
    public function readPagesByTitle(string $pageTitle, string $spaceKey = IRapiClientBase::REQ_VAL_SPACE_EMPTY): IResponse
    {
        self::$logger->debug('START - pageTitle,spaceKey', [$pageTitle, $spaceKey]);

        $prepareUrl = $this->prepareBrowseUrl($pageTitle, $spaceKey);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkPageExists(string $spaceKey, string $pageTitle, ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE): int
    {
        $pageId = IRapiClientBase::REQ_VAL_PAGE_ID_NO;
        $result = $this->readPagesByTitle($pageTitle, $spaceKey);

        if ($result->checkStatus() && $result->isResultsAvailable()) {
            $firstResult = $result->getResult(IRapiClientBase::RESP_VAL_RESULT_FIRST);
            $pageId = intval($firstResult[IResponse::KEY_ID]);
            self::$logger->info(str_repeat(' ', IRapiClientBase::VAL_LOG_SPACE) . 'Found item', [$spaceKey, $itemType->value, $pageTitle, $pageId]);
        } else {
            self::$logger->info(str_repeat(' ', IRapiClientBase::VAL_LOG_SPACE) . 'Not found item', [$spaceKey, $itemType->value, $pageTitle]);
        }

        return $pageId;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function scanPages(string $spaceKey = IRapiClientBase::REQ_VAL_SPACE_EMPTY): IResponse
    {
        self::$logger->debug('START - spaceKey', [$spaceKey]);

        $prepareUrl = $this->prepareScanUrl($spaceKey);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function searchPagesWithFilter(
        string $filterTerm,
        string $spaceKey,
        int $searchFromPos = IRapiClientBase::REQ_VAL_SEARCH_START,
        int $searchLimit = IRapiClientBase::REQ_VAL_SEARCH_LIMIT_MIN,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug(
            'START - filterTerm,spaceKey,searchFromPos,searchLimit,itemType',
            [$filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType]
        );
        $searchLimit = intval($searchLimit < IRapiClientBase::REQ_VAL_SEARCH_LIMIT_1ENTRY ? $this->constData->c(ConstData::KEY_SEARCH_LIMIT) : $searchLimit);
        $prepareUrl = $this->prepareSearchUrlExt($filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function spaceHomepage(string $spaceKey): int
    {
        self::$logger->debug('START - spaceKey', [$spaceKey]);

        $pageId = IRapiClientBase::RESP_VAL_PAGE_ID_NO;
        if (!empty($spaceKey)) {
            $prepareUrl = $this->prepareSpaceUrl($spaceKey);
            /** @var IResponse $result */
            $result = $this->exec($prepareUrl);
            if ($result->checkStatus()) {
                $pageId = $result->getValue(IResponse::KEY_HOMEPAGE, IRapiClientBase::RESP_VAL_PAGE_ID_NO);
                if (is_array($pageId)) {
                    $pageId = $pageId[IResponse::KEY_ID];
                }
            }
        }

        return intval($pageId);
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
        string $spaceKey = IRapiClientBase::REQ_VAL_SPACE_EMPTY,
        ItemTypeEnum $pageType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE,
        bool $withBody = IRapiClientBase::REQ_VAL_BODY_NO
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
        int $searchFromPos = IRapiClientBase::REQ_VAL_SEARCH_START_NO,
        int $searchLimit = IRapiClientBase::REQ_VAL_SEARCH_LIMIT_NO,
        ItemTypeEnum $pageType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE,
        bool $withBody = IRapiClientBase::REQ_VAL_BODY_NO
    ): string {
        $searchLimit = $searchLimit < IRapiClientBase::REQ_VAL_SEARCH_LIMIT_MIN ? $this->constData->c(ConstData::KEY_SEARCH_LIMIT) : $searchLimit;
        $prepareUrl = sprintf('%s?cql=', $this->constData->c(ConstData::KEY_CONF_SEARCH_URL));
        $prepareUrl .= sprintf('siteSearch~%s', urlencode("\"{$searchTerm}\""));
        $prepareUrl .= sprintf('+AND+space.type=%s', urlencode(SpaceTypeEnum::SPACE_TYPE_GLOBAL->value));
        $prepareUrl .= sprintf('+AND+type=%s', urlencode("\"{$pageType->value}\""));
        if (!empty($spaceKey)) {
            $prepareUrl .= sprintf('+AND+space=%s', urlencode("\"{$spaceKey}\""));
        }
        if ($searchFromPos >= IRapiClientBase::REQ_VAL_SEARCH_START_NO) {
            $prepareUrl .= sprintf('&start=%s&limit=%s', $searchFromPos, $searchLimit);
        }
        $prepareUrl .= sprintf('&%s', ($withBody ? QueryExtensionEnum::REQP_SEARCH_FULL->value : QueryExtensionEnum::REQP_SEARCH_LIGHT->value));

        return $prepareUrl;
    }

    protected function prepareBrowseUrl(string $pageTitle, string $spaceKey = IRapiClientBase::REQ_VAL_SPACE_EMPTY): string
    {
        $prepareUrl = sprintf('%s?title=%s&%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), urlencode($pageTitle), QueryExtensionEnum::REQP_LIGHT->value);

        return $this->addSpaceFilter($spaceKey, $prepareUrl);
    }

    protected function prepareScanUrl(string $spaceKey = IRapiClientBase::REQ_VAL_SPACE_EMPTY): string
    {
        $prepareUrl = sprintf('%s/scan?%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), QueryExtensionEnum::REQP_LIGHT->value);

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

    protected function prepareSpaceUrl(string $spaceKey): string
    {
        return sprintf('%s/%s?%s', $this->constData->c(ConstData::KEY_CONF_SPACE_URL), $spaceKey, QueryExtensionEnum::REQP_SPACE_LIST->value);
    }

    /**
     * @param IResponse $response
     *
     * @return int
     */
    protected function analyzeResponse(IResponse $response): int
    {
        return intval($response->getValue(IResponse::KEY_TOTAL_SIZE, 0));
    }
}
