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
use oglow\tools\Yacorapi\Data\SpaceTypeEnum;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use oglow\tools\Yacorapi\Statistic\IStatistic;

interface IRapiClientStatistic extends IRapiClientBase
{
    /**
     * Scans a space and count the items in the space.
     *
     * @param string       $spaceKey Limited to the space
     * @param ItemTypeEnum $itemType The type of the items (Default: PAGE);
     *
     * @return IStatistic The found and counted items
     */
    public function countItemsinSpace(string $spaceKey, ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE): IStatistic;

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
     * @param SpaceTypeEnum $spaceType The type of spaces (Default: global spaces)
     * @param int           $limit     The number of items which will be returned (Default: 100)
     *
     * @return IResponse All accessible Spaces or empty
     *
     * @see IRapiClientBase::SPACE_LIMIT_DEFAULT
     */
    public function listSpaces(SpaceTypeEnum $spaceType = SpaceTypeEnum::SPACE_TYPE_GLOBAL, int $limit = IRapiClientBase::REQ_VAL_SPACE_LIMIT_DEFAULT): IResponse;
}
