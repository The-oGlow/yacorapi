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

use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\Macro\AllAddon;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use oglow\tools\Yacorapi\Statistic\IStatistic;

interface IRapiClient
{
    /**
     * @param int $pageId
     *
     * @return IResponse
     */
    public function readPageByPageId(int $pageId): IResponse;

    /**
     * @param string $filterTerm
     * @param string $spaceKey
     *
     * @return IResponse
     */
    public function readPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse;

    /**
     * @param string $filterTerm
     * @param string $spaceKey
     *
     * @return IResponse
     */
    public function scanPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse;

    /**
     * @param string $filterTerm
     * @param string $spaceKey
     * @param int    $searchFromPos
     * @param int    $searchLimit
     * @param string $itemType
     *
     * @return IResponse
     */
    public function searchPagesWithFilter(
        string $filterTerm,
        string $spaceKey,
        int $searchFromPos = RequestParameterData::SEARCH_START,
        int $searchLimit = RequestParameterData::SEARCH_LIMIT_ZERO,
        string $itemType = RequestParameterData::ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * @param string $spaceKey
     * @param string $itemType
     *
     * @return IStatistic
     */
    public function countItemsinSpace(string $spaceKey, string $itemType = RequestParameterData::ITEM_TYPE_PAGE): IStatistic;

    /**
     * @param int $pageId
     *
     * @return IResponse
     */
    public function readRestrictionsByPageId(int $pageId): IResponse;

    /**
     * REFACTOR: API-Function doesn't work or description is wrong.
     *
     * @param int                $pageId
     * @param array<mixed,mixed> $writeRestrictions
     * @param array<mixed,mixed> $readRestrictions
     *
     * @return bool
     */
    public function writeRestrictionsByPageId(int $pageId, array $writeRestrictions = [], array $readRestrictions = []): bool;

    /**
     * REFACTOR: Listing only 100 spaces, loop is missing.
     *
     * @param string $spaceType
     * @param int    $limit
     *
     * @return IResponse
     */
    public function listSpaces(string $spaceType = RequestParameterData::SPACE_TYPE_GLOBAL, int $limit = RequestParameterData::SPACE_LIMIT_DEFAULT): IResponse;

    /**
     * @param string                     $spaceKey
     * @param ResponseAddonMacroDecorate $addonSet
     * @param null|IStatistic            $outputMatrix
     *
     * @return IStatistic
     */
    public function countMacrosInSpace(string $spaceKey, ResponseAddonMacroDecorate $addonSet, ?IStatistic $outputMatrix): IStatistic;

    /**
     * @param int $pageId
     * @param int $newParentId
     *
     * @return IResponse
     */
    public function movePage(int $pageId, int $newParentId): IResponse;

    /**
     * @param string   $spaceKey
     * @param string   $pageTitle
     * @param string   $pageBody
     * @param null|int $parentId
     * @param string   $itemType
     *
     * @return IResponse
     */
    public function createPage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        ?int $parentId = null,
        string $itemType = RequestParameterData::ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * @param int    $pageId
     * @param string $pageBody
     * @param string $pageTitle
     * @param string $comment
     * @param string $itemType
     *
     * @return IResponse
     */
    public function updatePage(
        int $pageId,
        string $pageBody,
        string $pageTitle = '',
        string $comment = '',
        string $itemType = RequestParameterData::ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * @param int $mode
     *
     * @return ResponseAddonMacroDecorate
     */
    public function prepareAddonSet(int $mode = AllAddon::ADDON_ALL): ResponseAddonMacroDecorate;
}
