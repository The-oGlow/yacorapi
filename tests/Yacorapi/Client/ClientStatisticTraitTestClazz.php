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
use oglow\tools\Yacorapi\Data\SpaceTypeEnum;

class ClientStatisticTraitTestClazz implements IRapiClientStatistic
{
    use ClientStatisticTrait;

    protected ConstData $constData;

    public function __construct()
    {
        $this->constData = new ConstData(ClientStatisticTraitTestClazz::class);
    }

    public function publicPrepareSpacePagesUrl(
        string $space,
        ItemTypeEnum $pageType = ItemTypeEnum::PAGE,
        int $start = ConstData::PAGE_START,
        int $limit = ConstData::PAGE_LIMIT
    ): string {
        return $this->prepareSpacePagesUrl($space, $pageType, $start, $limit);
    }

    public function publicPrepareSpaceListUrl(
        SpaceTypeEnum $spaceType = SpaceTypeEnum::SPACE_TYPE_GLOBAL,
        int $limit = ConstData::PAGE_LIMIT
    ): string {
        return $this->prepareSpaceListUrl($spaceType, $limit);
    }

    public function publicPrepareCountItemsUrl(ItemTypeEnum $itemType, string $spaceKey): string
    {
        return $this->prepareCountItemsUrl($itemType, $spaceKey);
    }
}
