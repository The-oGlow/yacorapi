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
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\Data\SpaceTypeEnum;
use oglow\tools\Yacorapi\Macro\AddonTypeEnum;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use oglow\tools\Yacorapi\Statistic\IStatistic;
use Psr\Log\LogLevel;

interface IRapiClient
{
    /** Default output level (INFO) */
    public const string LEVEL_DEFAULT = LogLevel::INFO;

    public const string MSG_PARENT_ID_MUST_BE_NUMERIC = 'parentId must be numeric!';

    public const string MSG_MOVED_TO_NEW_PARENT = 'Page moved to new parent ';

    public const string MSG_SPACE_IS_EMPTY = 'spaceKey is empty!';

    public const string MSG_UPDATE_PAGE_WITHOUT_CHANGES = 'Update page without changes';

    public const int REQ_SEARCH_FROM_POS = RequestParameterData::SEARCH_START;

    public const int REQ_SEARCH_LIMIT = RequestParameterData::SEARCH_LIMIT_ZERO;

    public const int REQ_SEARCH_LIMIT_1ENTRY = RequestParameterData::SEARCH_LIMIT_1ENTRY;

    public const ItemTypeEnum REQ_ITEM_TYPE_PAGE = ItemTypeEnum::PAGE;

    public const int REQ_NO_PARENT = RequestParameterData::NO_PARENT;

    public const int SPACE_LIMIT_DEFAULT = RequestParameterData::SPACE_LIMIT_DEFAULT;

    /**
     * Create new RapiClient.
     *
     * @param null|int                 $modeExtension
     * @param null|IConnectionProvider $connectionProvider
     * @param null|IContainer          $addons
     * @param int|LogLevel|string      $level              (Default: {@link IRapiClient::LEVEL_DEFAULT})
     *
     * @return IRapiClient
     *
     * @see IRapiClient::LEVEL_DEFAULT
     */
    public static function newClient(
        ?int $modeExtension = null,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClient::LEVEL_DEFAULT
    ): IRapiClient;

    /**
     * @return Set<string> All available REST-API methods
     *
     * @phpstan-return Set<non-empty-string>
     */
    public static function taskitemMethods(): Set;

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
     * @param string       $filterTerm    A search term from the confluence search
     * @param string       $spaceKey      Limited to the space
     * @param int          $searchFromPos Starting from which result position (Default: 0)
     * @param int          $searchLimit   The number of items which will be returned (Default: maximum)
     * @param ItemTypeEnum $itemType      The type of the items (Default: PAGE);
     *
     * @return IResponse The found pages or empty
     *
     * @see IRapiClient::scanPagesWithFilter()
     * @see IRapiClient::readPageByPageId()
     */
    public function searchPagesWithFilter(
        string $filterTerm,
        string $spaceKey,
        int $searchFromPos = IRapiClient::REQ_SEARCH_FROM_POS,
        int $searchLimit = IRapiClient::REQ_SEARCH_LIMIT,
        ItemTypeEnum $itemType = IRapiClient::REQ_ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * Scans a space and count the items in the space.
     *
     * @param string       $spaceKey Limited to the space
     * @param ItemTypeEnum $itemType The type of the items (Default: PAGE);
     *
     * @return IStatistic The found and counted items
     */
    public function countItemsinSpace(string $spaceKey, ItemTypeEnum $itemType = IRapiClient::REQ_ITEM_TYPE_PAGE): IStatistic;

    /**
     * Scans a space and count the macros in the space.
     *
     * @param string                     $spaceKey     Limited to the space
     * @param ResponseAddonMacroDecorate $addonSet     The set of addons containing the macros to scan for
     * @param IStatistic                 $outputMatrix An empty or previous statistic to add
     *
     * @return IStatistic The found and counted macros
     *
     * @see IRapiClient::prepareAddonSet()
     */
    public function countMacrosInSpace(string $spaceKey, ResponseAddonMacroDecorate $addonSet, IStatistic $outputMatrix): IStatistic;

    /**
     * REFACTOR: Listing only 100 spaces, loop is missing.
     *
     * @param SpaceTypeEnum $spaceType The type of spaces (Default: global spaces)
     * @param int           $limit     The number of items which will be returned (Default: 100)
     *
     * @return IResponse All accessible Spaces or empty
     *
     * @see IRapiClient::SPACE_LIMIT_DEFAULT
     */
    public function listSpaces(SpaceTypeEnum $spaceType = SpaceTypeEnum::SPACE_TYPE_GLOBAL, int $limit = IRapiClient::SPACE_LIMIT_DEFAULT): IResponse;

    /**
     * Creates a new confluence page in a space.
     *
     * @param string       $spaceKey  The space for the new page
     * @param string       $pageTitle The page title of the new page
     * @param string       $pageBody  The page body of the new page
     * @param int          $parentId  The target parent page for the new page
     * @param ItemTypeEnum $itemType  The type of the new page (Default: PAGE);
     *
     * @return IResponse The new created page or empty
     */
    public function createPage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        int $parentId = IRapiClient::REQ_NO_PARENT,
        ItemTypeEnum $itemType = IRapiClient::REQ_ITEM_TYPE_PAGE
    ): IResponse;

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
     * Change the content of a confluence page.
     *
     * @param int          $pageId    The id of the confluence page
     * @param string       $pageBody  The changed body for the page
     * @param string       $pageTitle The changed page title for the page
     * @param string       $comment   Describe the change (Default: '')
     * @param ItemTypeEnum $itemType  The type of the page (Default: PAGE);
     *
     * @return IResponse The changed page or empty
     *
     * @see IRapiClient::MSG_UPDATE_PAGE_WITHOUT_CHANGES
     */
    public function updatePage(
        int $pageId,
        string $pageBody,
        string $pageTitle = '',
        string $comment = '',
        ItemTypeEnum $itemType = IRapiClient::REQ_ITEM_TYPE_PAGE
    ): IResponse;

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
}
