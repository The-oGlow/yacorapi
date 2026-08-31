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
use oglow\tools\Yacorapi\IResponse;

interface IRapiClientWrite extends IRapiClientBase
{
    /**
     * Creates a new confluence page in a space.
     *
     * @param string       $spaceKey  The space for the new page
     * @param string       $pageTitle The page title of the new page
     * @param string       $pageBody  The page body of the new page
     * @param int          $parentId  The target parent page for the new page (Default: {@link IRapiClientBase::REQ_VAL_PARENT_ID_NO})
     * @param ItemTypeEnum $itemType  The type of the new page (Default: {@link IRapiClientBase::REQ_ITEM_TYPE_PAGE})
     *
     * @return IResponse The new created page or empty
     */
    public function createPage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * Change the content of a confluence page.
     *
     * @param int          $pageId    The id of the confluence page
     * @param string       $pageBody  The changed body for the page
     * @param string       $pageTitle The changed page title for the page (Default: '')
     * @param string       $comment   Describe the change (Default: '')
     * @param ItemTypeEnum $itemType  The type of the page (Default: {@link IRapiClientBase::REQ_ITEM_TYPE_PAGE})
     *
     * @return IResponse The changed page or empty
     */
    public function updatePage(
        int $pageId,
        string $pageBody,
        string $pageTitle = '',
        string $comment = '',
        ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * Creates a new page. If a page with the same page title in the space exists, the page will be updated.
     *
     * @param string       $spaceKey  The space for the new page
     * @param string       $pageTitle The page title of the new page
     * @param string       $pageBody  The page body of the new page
     * @param int          $parentId  The target parent page for the new page (Default: {@link IRapiClientBase::REQ_VAL_PARENT_ID_NO})
     * @param ItemTypeEnum $itemType  The type of the page (Default: {@link IRapiClientBase::REQ_ITEM_TYPE_PAGE})
     *
     * @return IResponse The new created or updated page or empty
     *
     * @see IRapiClientWrite::createPage($spaceKey, $pageTitle, $pageBody, $parentId, $itemType)
     * @see IRapiClientWrite::updatePage($pageId, $pageBody, $pageTitle, $comment, $itemType)
     */
    public function createOrUpdatePage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE
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
}
