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

use Ds\Map;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\AddonMacroData;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Macro\AllAddon;
use oglow\tools\Yacorapi\Request\RequestType;
use oglow\tools\Yacorapi\Response\Response;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use oglow\tools\Yacorapi\Response\ResponseSpaceDataDecorate;
use oglow\tools\Yacorapi\Statistic\IStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticTypeEnum;
use oglow\tools\Yacorapi\Statistic\ValueStatistic;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class RapiClient extends AbstractRapiClient // NOSONAR: php:S1448
{
    private static LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function newClient(?int $modeExtension = null, ?IConnectionProvider $connectionProvider = null, ?AddonMacroData $addons = null): IRapiClient
    {
        return new RapiClient($modeExtension, $connectionProvider, $addons);
    }

    /**
     * RapiClient constructor.
     *
     * @param null|int                 $modeExtension
     * @param null|IConnectionProvider $connectionProvider
     * @param null|AddonMacroData      $addons
     */
    protected function __construct(?int $modeExtension = null, ?IConnectionProvider $connectionProvider = null, ?AddonMacroData $addons = null)
    {
        // Init Logger
        self::$logger = new ConsoleLogger(name: RapiClient::class, level: self::LEVEL_DEFAULT);
        self::$logger->debug('START');

        parent::__construct($modeExtension, $connectionProvider, $addons);

        self::$logger->debug('END');
    }

    // Public methods

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readPageByPageId(int $pageId): IResponse
    {
        self::$logger->debug('START - pageId', [$pageId]);

        $prepareUrl = $this->commonExtension->prepareLoadUrl($pageId);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse
    {
        self::$logger->debug('START - filterTerm,spaceKey', [$filterTerm, $spaceKey]);

        $prepareUrl = $this->commonExtension->prepareBrowseUrl($filterTerm, $spaceKey);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function scanPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse
    {
        self::$logger->debug('START - filterTerm,spaceKey', [$filterTerm, $spaceKey]);

        $prepareUrl = $this->commonExtension->prepareScanUrl($filterTerm, $spaceKey);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function searchPagesWithFilter(
        string $filterTerm,
        string $spaceKey,
        int $searchFromPos = RequestParameterData::SEARCH_START,
        int $searchLimit = RequestParameterData::SEARCH_LIMIT_ZERO,
        string $itemType = RequestParameterData::ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug(
            'START - filterTerm,spaceKey,searchFromPos,searchLimit,itemType',
            [$filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType]
        );
        $searchLimit = (int) ($searchLimit < RequestParameterData::SEARCH_LIMIT_1ENTRY ? $this->constData->c(ConstData::KEY_SEARCH_LIMIT) : $searchLimit);
        $prepareUrl = $this->commonExtension->prepareSearchUrlExt($filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function countItemsinSpace(string $spaceKey, string $itemType = RequestParameterData::ITEM_TYPE_PAGE): IStatistic
    {
        self::$logger->debug('START - spaceKey, itemType', [$spaceKey, $itemType]);

        $prepareUrl = $this->commonExtension->prepareCountItemsUrl($itemType, $spaceKey);
        $response = $this->exec($prepareUrl);

        $itemCount = $response->getValue(IResponse::KEY_TOTAL_SIZE, 0);
        $valueStatistic = new ValueStatistic(ValueStatistic::EMPTY_STRING, null);
        $valueStatistic->addItem(ValueStatistic::EMPTY_STRING, $itemCount);
        $singleStatistic = new StatisticStatistic($itemType, StatisticTypeEnum::PAGETYPE);
        $singleStatistic->addItem($itemType, $valueStatistic);

        self::$logger->debug('END');

        return $singleStatistic;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readRestrictionsByPageId(int $pageId): IResponse
    {
        self::$logger->debug('START - pageId', [$pageId]);

        $prepareUrl = $this->adminExtension->prepareRestrictByOpUrl($pageId);

        return $this->exec($prepareUrl);
    }

    /**
     * REFACTOR: API-Function doesn't work or description is wrong.
     *
     * @inheritDoc
     */
    #[\Override]
    public function writeRestrictionsByPageId(int $pageId, array $writeRestrictions = [], array $readRestrictions = []): bool // NOSONAR: php:S1172
    {
        throw new \BadMethodCallException('API-Function does not work or description is wrong');
    }

    /**
     * REFACTOR: Listing only 100 spaces, loop is missing.
     *
     * @inheritDoc
     */
    #[\Override]
    public function listSpaces(string $spaceType = RequestParameterData::SPACE_TYPE_GLOBAL, int $limit = RequestParameterData::SPACE_LIMIT_DEFAULT): IResponse
    {
        self::$logger->debug('START - spaceType,limit', [$spaceType, $limit]);

        $prepareUrl = $this->commonExtension->prepareSpaceListUrl($spaceType, $limit);

        return new ResponseSpaceDataDecorate($this->exec($prepareUrl));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function countMacrosInSpace(string $spaceKey, ResponseAddonMacroDecorate $addonSet, ?IStatistic $outputMatrix): IStatistic
    {
        self::$logger->debug('START - spaceKey,addonSet', [$spaceKey, $addonSet]);

        $mapAddons = $addonSet->getResponse();

        $response = $this->loopAddons($spaceKey, $addonSet->getMode(), $mapAddons, $outputMatrix);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function movePage(int $pageId, int $newParentId): IResponse
    {
        self::$logger->debug('START - pageId,newParentId', [$pageId, $newParentId]);

        $page = $this->readPageByPageId($pageId);

        $pageVersion = $page->getValue(RequestParameterData::PROP_VERSION, []);
        if (is_array($pageVersion) && array_key_exists(RequestParameterData::PROP_NUMBER, $pageVersion)) {
            $pageVersion = (int) $pageVersion[RequestParameterData::PROP_NUMBER];
        } else {
            $pageVersion = 1;
        }

        $parameters = new Map();
        $parameters->put(RequestParameterData::PROP_TITLE, $page->getValue(RequestParameterData::PROP_TITLE));
        $parameters->put(RequestParameterData::PROP_TYPE, $page->getValue(RequestParameterData::PROP_TYPE));
        $parameters->put(RequestParameterData::PROP_ANCESTORS, [[RequestParameterData::PROP_ID => $newParentId]]);
        $parameters->put(
            RequestParameterData::PROP_VERSION,
            [
                RequestParameterData::PROP_NUMBER => ++$pageVersion, // NOSONAR php:S881
                RequestParameterData::PROP_MESSAGE => self::MSG_MOVED_TO_NEW_PARENT . $newParentId,
            ]
        );

        $prepareUrl = $this->commonExtension->prepareUpdateURL($pageId);
        $response = $this->execPost($prepareUrl, $parameters, RequestType::REQ_TYP_PUT);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function createPage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        ?int $parentId = null,
        string $itemType = RequestParameterData::ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - spaceKey,pageTitle,parentId,pageType,pageBody', [$spaceKey, $pageTitle, $parentId, $itemType, empty($pageBody)]);

        /** @var Map<mixed,mixed> $parameters */
        $parameters = new Map(
            [
            RequestParameterData::PROP_TYPE => $itemType,
            RequestParameterData::PROP_TITLE => $pageTitle,
            RequestParameterData::PROP_STATUS => RequestParameterData::STATUS_TYPE_CURRENT,
            RequestParameterData::PROP_BODY => [
                RequestParameterData::PROP_STORAGE => [
                    RequestParameterData::PROP_VALUE => $pageBody,
                    RequestParameterData::PROP_REPRESENTATION => RequestParameterData::REPRESENTATION_TYPE_STORAGE,
                ],
            ],
            ]
        );
        if (empty($spaceKey)) {
            throw new \InvalidArgumentException(self::MSG_SPACE_IS_EMPTY);
        } else {
            $parameters->put(RequestParameterData::PROP_SPACE, [RequestParameterData::PROP_KEY => $spaceKey]);
        }
        if (is_numeric($parentId)) {
            $parameters->put(RequestParameterData::PROP_ANCESTORS, [RequestParameterData::PROP_ID => $parentId]);
        } else {
            throw new \InvalidArgumentException(self::MSG_PARENT_ID_MUST_BE_NUMERIC);
        }
        $prepareUrl = $this->commonExtension->prepareCreatePage();
        $response = $this->execPost($prepareUrl, $parameters, RequestType::REQ_TYP_POST);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function updatePage(
        int $pageId,
        string $pageBody,
        string $pageTitle = '',
        string $comment = '',
        string $itemType = RequestParameterData::ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - pageId,pageTitle,pageType,bodySize,comment', [$pageId, $pageTitle, $itemType, strlen($pageBody), $comment]);

        $response = new Response();

        if (!empty($pageId)) {
            $currentPage = $this->readPageByPageId($pageId);
            $currentVersion = (int) $currentPage->getValue(RequestParameterData::PROP_VERSION, 1);
            $nextVersion = $currentVersion + 1;

            if (empty($comment)) {
                $comment = self::MSG_UPDATE_PAGE_WITHOUT_CHANGES;
            }
            $prepareURL = $this->commonExtension->prepareUpdateURL($pageId);
            $parameters = new Map(
                [
                RequestParameterData::PROP_ID => $pageId,
                RequestParameterData::PROP_TYPE => $itemType,
                RequestParameterData::PROP_TITLE => $pageTitle,
                RequestParameterData::PROP_BODY => [
                    RequestParameterData::PROP_STORAGE => [
                        RequestParameterData::PROP_VALUE => $pageBody,
                        RequestParameterData::PROP_REPRESENTATION => RequestParameterData::REPRESENTATION_TYPE_STORAGE,
                    ],
                ],
                RequestParameterData::PROP_VERSION => [RequestParameterData::PROP_NUMBER => $nextVersion, RequestParameterData::PROP_MESSAGE => $comment],
                ]
            );

            $response = $this->execPost($prepareURL, $parameters, RequestType::REQ_TYP_PUT);
            $success = $response->checkStatus();
            self::$logger->debug('Update page with title', [$pageId, $pageTitle, ($success ? 'successful' : 'failed')]);
        } else {
            self::$logger->error('No correct pageId', [$pageId]);
        }
        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function prepareAddonSet($mode = AllAddon::ADDON_ALL): ResponseAddonMacroDecorate
    {
        self::$logger->debug('START - mode', [$mode]);

        $data = $this->addons->getDataByMode($mode);
        if (!empty($data)) {
            /** @psalm-suppress MixedMethodCall */
            $addonSet = new ResponseAddonMacroDecorate($mode, $data->toArray());
        } else {
            $addonSet = new ResponseAddonMacroDecorate($mode);
        }
        self::$logger->debug('END');

        return $addonSet;
    }

    // Private methods
}
