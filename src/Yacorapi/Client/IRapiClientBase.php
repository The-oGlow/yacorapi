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
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\IResponse;
use Psr\Log\LogLevel;

interface IRapiClientBase
{
    /** Default output level (INFO) */
    public const string LEVEL_DEFAULT = LogLevel::INFO;

    // Common Parameter
    public const ExtensionEnum EXTENSION_DEFAULT = ExtensionEnum::EXTENSION_ALL;

    // Request Parameter
    public const int REQ_VAL_SPACE_LIMIT_DEFAULT = RequestParameterData::VAL_SPACE_LIMIT_DEFAULT;

    public const int REQ_VAL_SEARCH_START = RequestParameterData::VAL_SEARCH_START;

    public const int REQ_VAL_SEARCH_LIMIT_MIN = RequestParameterData::VAL_SEARCH_LIMIT_MIN;

    public const int REQ_VAL_SEARCH_LIMIT_1ENTRY = RequestParameterData::VAL_SEARCH_LIMIT_1ENTRY;

    public const ItemTypeEnum REQ_ITEM_TYPE_PAGE = ItemTypeEnum::PAGE;

    public const int REQ_VAL_PARENT_ID_NO = RequestParameterData::VAL_PARENT_ID_NO;

    public const int REQ_VAL_PAGE_ID_NO = RequestParameterData::VAL_PAGE_ID_NO;

    public const string REQ_VAL_SPACE_EMPTY = RequestParameterData::VAL_SPACE_EMPTY;

    public const string REQ_VAL_BODY_EMPTY = RequestParameterData::VAL_BODY_EMPTY;

    public const int REQ_VAL_VERSION_FIRST = RequestParameterData::VAL_VERSION_FIRST;

    // Response Parameter
    public const int RESP_VAL_PAGE_ID_NO = IResponse::VAL_PAGE_ID_NO;

    public const int RESP_VAL_VERSION_NO = IResponse::VAL_VERSION_NO;

    public const string RESP_VAL_TITLE_EMPTY = IResponse::VAL_TITLE_EMPTY;
}
