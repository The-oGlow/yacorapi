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

namespace oglow\tools\Yacorapi\Data;

use Ds\Map;
use oglow\tools\common\AbstractSingleton;

class RequestParameterData extends AbstractSingleton
{
    // Expand Consts
    public const REQP_SPACE_LIST = 'expand=homepage,description.plain,metadata.labels';

    public const REQP_FULL = 'expand=space,history,version,body.storage,restrictions.read.restrictions.user,' .
        'restrictions.read.restrictions.group,restrictions.read,restrictions.update.restrictions.user,' .
        'restrictions.update.restrictions.group';

    public const REQP_SEARCH_FULL = 'expand=content.space,content.history,content.version,content.body.storage';

    public const REQP_LIGHT = 'expand=space,history,version';

    public const REQP_PERM = 'expand=restrictions.read.restrictions.user,restrictions.read.restrictions.group,' .
        'restrictions.read,restrictions.update.restrictions.user,restrictions.update.restrictions.group';

    public const REQP_SEARCH_LIGHT = 'expand=content.space,content.history,content.version';

    public const REQP_RESTRICTIONS_FULL = 'expand=read.restrictions.user,read.restrictions.group,update.restrictions.user,update.restrictions.group';

    public const RESP_CSV_SPACE_RESULTS = ' .results[]|.key + ";" + .type + ";" + "status" + ";"'
        . ' + "\"" + .name + "\"" + ";" + "\"" + .description.plain.value +';

    // Item Type Consts
    public const ITEM_TYPE_PAGE = 'page';

    public const ITEM_TYPES = [self::ITEM_TYPE_PAGE, 'attachment', 'blogpost'];

    public const ITEM_TYPES_ALL = [self::ITEM_TYPE_PAGE, 'attachment', 'blogpost', 'comment'];

    // Property Consts
    public const PROP_ANCESTORS = 'ancestors';

    public const PROP_BODY = 'body';

    public const PROP_CONTENT = 'content';

    public const PROP_GROUP = 'group';

    public const PROP_ID = 'key';

    public const PROP_KEY = 'key';

    public const PROP_MESSAGE = 'message';

    public const PROP_NUMBER = 'number';

    public const PROP_REPRESENTATION = 'representation';

    public const PROP_SPACE = 'space';

    public const PROP_SPACE_KEY = 'spaceKey';

    public const PROP_STATUS = 'status';

    public const PROP_STORAGE = 'storage';

    public const PROP_TITLE = 'title';

    public const PROP_TYPE = 'type';

    public const PROP_USER = 'user';

    public const PROP_USERNAME = 'username';

    public const PROP_VALUE = 'value';

    public const PROP_VERSION = 'version';

    // Space Consts
    public const SPACE_LIMIT_DEFAULT = 100;

    public const SPACE_TYPE_GLOBAL = 'global';

    public const SPACE_TYPE_PERSONAL = 'personal';

    // Page Consts
    public const PAGE_COUNT = 'count';

    public const PAGE_TYPE = 'pagetype';

    // Property Values Consts
    public const REPRESENTATION_TYPE_STORAGE = 'storage';

    public const STATUS_TYPE_CURRENT = 'current';

    public const SEARCH_START = 0;

    public const SEARCH_LIMIT_ZERO = 0;

    public const SEARCH_LIMIT_1ENTRY = 1;

    public const SEARCH_LIMIT_MAX = 100;

    public const USER_TYPE_KNOWN = 'known';

    protected function prepareSettings(): void
    {
        // NothingToDo
    }

    /**
     * @param Map<mixed, mixed> $overrideParameters
     *
     * @return bool
     */
    final protected function validateSettings(Map $overrideParameters): bool
    {
        return true;
    }
}
