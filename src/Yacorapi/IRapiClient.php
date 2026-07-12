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

namespace oglow\tools\Yacorapi;

use Ds\Set;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\Macro\AllAddon;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use oglow\tools\Yacorapi\Statistic\IStatistic;

interface IRapiClient
{
    /**
     * @return Set<string> All available REST-API methods
     *
     * @phpstan-return Set<non-empty-string>
     */
    public static function rapiMethods(): Set;

    /**
     * Loads a confluence page by its page id.
     *
     * @param int $pageId The id of the confluence page
     *
     * @return IResponse The found page or empty
     */
    public function readPageByPageId(int $pageId): IResponse;

    /**
     * Searchs for confluence pages by a filter term.
     *
     * @param string $filterTerm A search term from the confluence search
     * @param string $spaceKey   Limited to the space (Default: '')
     *
     * @return IResponse The found pages or empty
     *
     * @see IRapiClient::scanPagesWithFilter()
     * @see IRapiClient::searchPagesWithFilter()
     */
    public function readPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse;

    /**
     * Scans for confluence pages by a filter term.
     *
     * @param string $filterTerm A search term from the confluence search
     * @param string $spaceKey   Limited to the space (Default: '')
     *
     * @return IResponse The found pages or empty
     *
     * @see IRapiClient::readPageByPageId()
     * @see IRapiClient::searchPagesWithFilter()
     */
    public function scanPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse;

    /**
     * Extend search for confluence pages by a filter term in one space.
     *
     * @param string $filterTerm    A search term from the confluence search
     * @param string $spaceKey      Limited to the space
     * @param int    $searchFromPos Starting from which result position (Default: 0)
     * @param int    $searchLimit   The number of items which will be returned (Default: maximum)
     * @param string $itemType      The type of the items (Default: PAGE);
     *
     * @return IResponse The found pages or empty
     *
     * @see IRapiClient::scanPagesWithFilter()
     * @see IRapiClient::readPageByPageId()
     * @see RequestParameterData::ITEM_TYPE_PAGE
     */
    public function searchPagesWithFilter(
        string $filterTerm,
        string $spaceKey,
        int $searchFromPos = RequestParameterData::SEARCH_START,
        int $searchLimit = RequestParameterData::SEARCH_LIMIT_ZERO,
        string $itemType = RequestParameterData::ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * Scans a space and count the items in the space.
     *
     * @param string $spaceKey Limited to the space
     * @param string $itemType The type of the items (Default: PAGE);
     *
     * @return IStatistic The found and counted items
     *
     * @see RequestParameterData::ITEM_TYPE_PAGE
     */
    public function countItemsinSpace(string $spaceKey, string $itemType = RequestParameterData::ITEM_TYPE_PAGE): IStatistic;

    /**
     * Load the restrictions for this confluence page.
     *
     * @param int $pageId The id of the confluence page
     *
     * @return IResponse The page restrictions for the page or empty
     */
    public function readRestrictionsByPageId(int $pageId): IResponse;

    /**
     * Set page restrictions (read/write) for the confluence page.
     * REFACTOR: API-Function doesn't work or description is wrong.
     *
     * @param int                $pageId            The id of the confluence page
     * @param array<mixed,mixed> $writeRestrictions Write restrictions for the page
     * @param array<mixed,mixed> $readRestrictions  Read restrictions for the page
     *
     * @return bool TRUE=Restrictions are set properly, else FALSE
     */
    public function writeRestrictionsByPageId(int $pageId, array $writeRestrictions = [], array $readRestrictions = []): bool;

    /**
     * REFACTOR: Listing only 100 spaces, loop is missing.
     *
     * @param string $spaceType The type of spaces (Default: global spaces)
     * @param int    $limit     The number of items which will be returned (Default: 100)
     *
     * @return IResponse All accessible Spaces or empty
     *
     * @see RequestParameterData::SPACE_TYPE_GLOBAL
     * @see RequestParameterData::SPACE_LIMIT_DEFAULT
     */
    public function listSpaces(string $spaceType = RequestParameterData::SPACE_TYPE_GLOBAL, int $limit = RequestParameterData::SPACE_LIMIT_DEFAULT): IResponse;

    /**
     * Scans a space and count the macros in the space.
     *
     * @param string                     $spaceKey     Limited to the space
     * @param ResponseAddonMacroDecorate $addonSet     The set of addons containing the macros to scan for
     * @param IStatistic            $outputMatrix An empty or previous statistic to add
     *
     * @return IStatistic The found and counted macros
     *
     * @see IRapiClient::prepareAddonSet()
     */
    public function countMacrosInSpace(string $spaceKey, ResponseAddonMacroDecorate $addonSet, IStatistic $outputMatrix): IStatistic;

    /**
     * Moves a confluence page from one parent page to another parent page.
     *
     * @param int $pageId      The id of the confluence page
     * @param int $newParentId The target parent page
     *
     * @return IResponse The moved page or empty
     */
    public function movePage(int $pageId, int $newParentId): IResponse;

    /**
     * Creates a new confluence page in a space.
     *
     * @param string   $spaceKey  The space for the new page
     * @param string   $pageTitle The page title of the new page
     * @param string   $pageBody  The page body of the new page
     * @param int $parentId  The target parent page for the new page
     * @param string   $itemType  The type of the new page (Default: PAGE);
     *
     * @return IResponse The new created page or empty
     *
     * @see RequestParameterData::ITEM_TYPE_PAGE
     */
    public function createPage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        int $parentId = RequestParameterData::NO_PARENT,
        string $itemType = RequestParameterData::ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * Change the content of a confluence page.
     *
     * @param int    $pageId    The id of the confluence page
     * @param string $pageBody  The changed body for the page
     * @param string $pageTitle The changed page title for the page
     * @param string $comment   Describe the change (Default: '')
     * @param string $itemType  The type of the page (Default: PAGE);
     *
     * @return IResponse The changed page or empty
     *
     * @see IRapiClient::MSG_UPDATE_PAGE_WITHOUT_CHANGES
     * @see RequestParameterData::ITEM_TYPE_PAGE
     */
    public function updatePage(
        int $pageId,
        string $pageBody,
        string $pageTitle = '',
        string $comment = '',
        string $itemType = RequestParameterData::ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * Provide a set of addons, containing macros.
     *
     * @param int $mode Predefined set of addons (Default: all addons)
     *
     * @return ResponseAddonMacroDecorate Set of Addons or empty
     *
     * @see AllAddon::ADDON_ALL
     * @see IRapiClient::countMacrosInSpace()
     */
    public function prepareAddonSet(int $mode = AllAddon::ADDON_ALL): ResponseAddonMacroDecorate;
}
