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

    public const ExtensionEnum EXTENSION_DEFAULT = ExtensionEnum::EXTENSION_ALL;
    
    public const int REQ_SEARCH_FROM_POS = RequestParameterData::SEARCH_START;

    public const int REQ_SEARCH_LIMIT = RequestParameterData::SEARCH_LIMIT_ZERO;

    public const int REQ_SEARCH_LIMIT_1ENTRY = RequestParameterData::SEARCH_LIMIT_1ENTRY;

    public const ItemTypeEnum REQ_ITEM_TYPE_PAGE = ItemTypeEnum::PAGE;

    public const int REQ_NO_PARENT = RequestParameterData::NO_PARENT;

    public const int REQ_NO_PAGE_ID = RequestParameterData::NO_PAGE_ID;
    
    public const string REQ_NO_SPACE = RequestParameterData::NO_SPACE;
    public const string REQ_EMPTY_BODY = RequestParameterData::EMPTY_BODY;
    
    public const int SPACE_LIMIT_DEFAULT = RequestParameterData::SPACE_LIMIT_DEFAULT;

    public const int RESP_NO_PAGE_ID = IResponse::NO_PAGE_ID;
    
    public const int RESP_NO_VERSION = IResponse::NO_VERSION;
    
    public const string RESP_EMPTY_TITLE = IResponse::EMPTY_TITLE;
}
