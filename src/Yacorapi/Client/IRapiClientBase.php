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

use Ds\Set;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\Macro\AddonTypeEnum;
use oglow\tools\Yacorapi\Request\RequestParameter;
use oglow\tools\Yacorapi\Response\ResponseParameter;
use Psr\Log\LogLevel;

/**
 * @author olliy
 */
interface IRapiClientBase
{
    /** @var string Default output level */
    public const string LEVEL_DEFAULT = 'info';

    // Common Parameter
    public const AddonTypeEnum ADDON_DEFAULT = AddonTypeEnum::ADDON_ALL;

    public const ExtensionEnum EXTENSION_DEFAULT = ExtensionEnum::EXTENSION_ALL;

    public const int VAL_COMMENT_MAXLEN = RequestParameter::VAL_COMMENT_MAXLEN;

    public const int VAL_LOG_SPACE = 100;
    // Request Parameter

    public const string REQ_VAL_BODY_EMPTY = RequestParameter::VAL_BODY_EMPTY;

    public const bool REQ_VAL_BODY_NO = RequestParameter::VAL_BODY_NO;

    public const string REQ_VAL_COMMENT_EMPTY = RequestParameter::VAL_COMMENT_EMPTY;

    public const ItemTypeEnum REQ_VAL_ITEM_TYPE_PAGE = ItemTypeEnum::PAGE;

    public const int REQ_VAL_PAGE_ID_NO = RequestParameter::VAL_PAGE_ID_NO;

    public const string REQ_VAL_PAGE_TITLE_EMPTY = RequestParameter::VAL_PAGE_TITLE_EMPTY;

    public const int REQ_VAL_PARENT_ID_NO = RequestParameter::VAL_PARENT_ID_NO;

    public const int REQ_VAL_SEARCH_LIMIT_MIN = RequestParameter::VAL_SEARCH_LIMIT_MIN;

    public const int REQ_VAL_SEARCH_LIMIT_1ENTRY = RequestParameter::VAL_SEARCH_LIMIT_1ENTRY;

    public const int REQ_VAL_SEARCH_LIMIT_NO = RequestParameter::VAL_SEARCH_LIMIT_NO;

    public const int REQ_VAL_SEARCH_START = RequestParameter::VAL_SEARCH_START;

    public const int REQ_VAL_SEARCH_START_NO = RequestParameter::VAL_SEARCH_START_NO;

    public const int REQ_VAL_SPACE_LIMIT_DEFAULT = RequestParameter::VAL_SPACE_LIMIT_DEFAULT;

    public const string REQ_VAL_SPACE_EMPTY = RequestParameter::VAL_SPACE_EMPTY;

    public const int REQ_VAL_VERSION_FIRST = RequestParameter::VAL_VERSION_FIRST;

    // Response Parameter
    public const string RESP_VAL_BODY_EMPTY = ResponseParameter::VAL_BODY_EMPTY;

    public const int RESP_VAL_PAGE_ID_NO = ResponseParameter::VAL_PAGE_ID_NO;

    public const int RESP_VAL_RESULT_FIRST = ResponseParameter::VAL_RESULT_FIRST;

    public const string RESP_VAL_TITLE_EMPTY = ResponseParameter::VAL_TITLE_EMPTY;

    public const int RESP_VAL_VERSION_NO = ResponseParameter::VAL_VERSION_NO;

    /**
     * Create new RapiClient.
     *
     * @param null|ExtensionEnum       $modeExtension      (Default: {@link IRapiClientBase::EXTENSION_DEFAULT})
     * @param null|IConnectionProvider $connectionProvider
     * @param null|IContainer          $addons
     * @param int|LogLevel|string      $level              (Default: {@link IRapiClientBase::LEVEL_DEFAULT})
     *
     * @return IRapiClient
     */
    public static function newClient(
        ?ExtensionEnum $modeExtension = IRapiClientBase::EXTENSION_DEFAULT,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ): IRapiClient;

    /**
     * @return Set<string> All available REST-API methods
     *
     * @phpstan-return Set<non-empty-string>
     */
    public static function taskitemMethods(): Set;
}
