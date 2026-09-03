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
     * Creates a new confluence page (confluence item) in a space.
     *
     * @param string       $spaceKey  The space for the new page
     * @param string       $pageTitle The page title of the new page
     * @param string       $pageBody  The page body of the new page
     * @param int          $parentId  The target parent page for the new page (Default: {@link IRapiClientBase::REQ_VAL_PARENT_ID_NO})
     * @param string       $comment   Describe the creation (Default: {@link IRapiClientBase::REQ_VAL_COMMENT_EMPTY})
     * @param ItemTypeEnum $itemType  The type of the confluence item (Default: {@link IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE})
     *
     * @return IResponse The new created page (confluence item) or empty response
     */
    public function createPage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * Change the content of a confluence page (confluence item).
     *
     * @param int          $pageId    The id of the confluence page
     * @param string       $pageBody  The changed body for the page {@link IRapiClientBase::REQ_VAL_BODY_EMPTY})
     * @param string       $pageTitle The same or changed page title for the page (Default: {@link IRapiClientBase::REQ_VAL_PAGE_TITLE_EMPTY})
     * @param string       $comment   Describe the change (Default: {@link IRapiClientBase::REQ_VAL_COMMENT_EMPTY})
     * @param ItemTypeEnum $itemType  The type of the confluence item (Default: {@link IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE})
     *
     * @return IResponse The changed page (confluence item) or empty response
     */
    public function updatePage(
        int $pageId,
        string $pageBody = IRapiClientBase::REQ_VAL_BODY_EMPTY,
        string $pageTitle = IRapiClientBase::REQ_VAL_PAGE_TITLE_EMPTY,
        string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * Creates a new page (confluence item).<br/>
     * If a page (confluence item) with the same page title in the space exists, the page (confluence item) will be updated.
     *
     * @param string       $spaceKey  The space for the new page
     * @param string       $pageTitle The page title of the new page
     * @param string       $pageBody  The page body of the new page
     * @param int          $parentId  The target parent page for the new page (Default: {@link IRapiClientBase::REQ_VAL_PARENT_ID_NO})
     * @param string       $comment   Describe the creation/change (Default: {@link IRapiClientBase::REQ_VAL_COMMENT_EMPTY})
     * @param ItemTypeEnum $itemType  The type of the confluence item (Default: {@link IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE})
     *
     * @return IResponse The new created or updated page (confluence item) or empty response
     *
     * @see IRapiClientWrite::createPage($spaceKey, $pageTitle, $pageBody, $parentId, $comment, $itemType)
     * @see IRapiClientWrite::updatePage($pageId, $pageBody, $pageTitle, $comment, $comment, $itemType)
     */
    public function createOrUpdatePage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE
    ): IResponse;

    /**
     * Moves a page (confluence item) from one parent page to another parent page.
     *
     * @param int    $pageId      The id of the confluence page
     * @param int    $newParentId The target parent page
     * @param string $comment     Describe the move (Default: {@link IRapiClientBase::REQ_VAL_COMMENT_EMPTY})
     *
     * @return IResponse The moved page (confluence item) or empty response
     */
    public function movePage(int $pageId, int $newParentId, string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY): IResponse;
}
