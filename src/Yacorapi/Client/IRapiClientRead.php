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

use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Macro\AddonTypeEnum;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;

interface IRapiClientRead extends IRapiClientBase
{
    /**
     * Provide a set of addons, containing macros.
     *
     * @param AddonTypeEnum $mode Predefined set of addons (Default: all addons)
     *
     * @return ResponseAddonMacroDecorate Set of Addons or empty
     *
     * @see AddonTypeEnum::ADDON_ALL
     * @see IRapiClient::countMacrosInSpace()
     */
    public function prepareAddonSet(AddonTypeEnum $mode = AddonTypeEnum::ADDON_ALL): ResponseAddonMacroDecorate;

    /**
     * Loads a confluence page by its page id.
     *
     * @param int $pageId The id of the confluence page
     *
     * @return IResponse The found page or empty
     */
    public function readPageByPageId(int $pageId): IResponse;

    /**
     * Searchs for confluence pages by page title.
     *
     * @param string $pageTitle Name of the page
     * @param string $spaceKey  Limited to the space (Default: '')
     *
     * @return IResponse The found pages or empty
     */
    public function readPagesByTitle(string $pageTitle, string $spaceKey = RequestParameterData::VAL_SPACE_EMPTY): IResponse;

    /**
     * Verifies if an item with a certain title exists in a space.
     *
     * @param string       $spaceKey  The space to look in
     * @param string       $pageTitle The complete name of the page title
     * @param ItemTypeEnum $itemType  The type of the item (Default {@link ItemTypeEnum::PAGE}
     *
     * @return int The page id of the page or {@link IResponse::VAL_PAGE_ID_NO}
     *
     * @see IResponse::VAL_PAGE_ID_NO
     * @see ItemTypeEnum::PAGE
     */
    public function checkPageExists(string $spaceKey, string $pageTitle, ItemTypeEnum $itemType = ItemTypeEnum::PAGE): int;

    /**
     * Scans for confluence pages by space.
     *
     * @param string $spaceKey Limited to the space (Default: '')
     *
     * @return IResponse The found pages or empty
     */
    public function scanPages(string $spaceKey = RequestParameterData::VAL_SPACE_EMPTY): IResponse;

    /**
     * Extend search for confluence pages by a filter term in one space.
     *
     * @param string       $filterTerm    A search term from the confluence search
     * @param string       $spaceKey      Limited to the space
     * @param int          $searchFromPos Starting from which result position (Default: 0)
     * @param int          $searchLimit   The number of items which will be returned (Default: maximum)
     * @param ItemTypeEnum $itemType      The type of the items (Default: PAGE);
     *
     * @return IResponse The found pages or empty
     */
    public function searchPagesWithFilter(
        string $filterTerm,
        string $spaceKey,
        int $searchFromPos = IRapiClientBase::REQ_VAL_SEARCH_START,
        int $searchLimit = IRapiClientBase::REQ_VAL_SEARCH_LIMIT_MIN,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE
    ): IResponse;

    /*
     *
     * @param string       $spaceKey      space
     * @return int PageId of the homepage or -1 if not found
     *
     * @see IRapiClientBase::RESP_VAL_PAGE_ID_NO
     */

    public function spaceHomepage(string $spaceKey): int;
}
