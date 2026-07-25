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
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Macro\AddonTypeEnum;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;

trait RapiReadTrait
{
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
        int $searchFromPos = IRapiClient::REQ_SEARCH_FROM_POS,
        int $searchLimit = IRapiClient::REQ_SEARCH_LIMIT,
        ItemTypeEnum $itemType = IRapiClient::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug(
            'START - filterTerm,spaceKey,searchFromPos,searchLimit,itemType',
            [$filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType]
        );
        $searchLimit = (int) ($searchLimit < IRapiClient::REQ_SEARCH_LIMIT_1ENTRY ? $this->constData->c(ConstData::KEY_SEARCH_LIMIT) : $searchLimit);
        $prepareUrl = $this->commonExtension->prepareSearchUrlExt($filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType);

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
}
