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
    public const string REQP_SPACE_LIST = 'expand=homepage,description.plain,metadata.labels';

    public const string REQP_FULL = 'expand=space,history,version,body.storage,restrictions.read.restrictions.user,' .
        'restrictions.read.restrictions.group,restrictions.read,restrictions.update.restrictions.user,' .
        'restrictions.update.restrictions.group';

    public const string REQP_SEARCH_FULL = 'expand=content.space,content.history,content.version,content.body.storage';

    public const string REQP_LIGHT = 'expand=space,history,version';

    public const string REQP_PERM = 'expand=restrictions.read.restrictions.user,restrictions.read.restrictions.group,' .
        'restrictions.read,restrictions.update.restrictions.user,restrictions.update.restrictions.group';

    public const string REQP_SEARCH_LIGHT = 'expand=content.space,content.history,content.version';

    public const string REQP_RESTRICTIONS_FULL = 'expand=read.restrictions.user,read.restrictions.group,update.restrictions.user,update.restrictions.group';

    public const string RESP_CSV_SPACE_RESULTS = ' .results[]|.key + ";" + .type + ";" + "status" + ";"'
        . ' + "\"" + .name + "\"" + ";" + "\"" + .description.plain.value +';

    // Item Type Consts
    public const string ITEM_TYPE_PAGE = 'page';

    public const array ITEM_TYPES = [self::ITEM_TYPE_PAGE, 'attachment', 'blogpost'];

    public const array ITEM_TYPES_ALL = [self::ITEM_TYPE_PAGE, 'attachment', 'blogpost', 'comment'];

    // Property Consts
    public const string PROP_ANCESTORS = 'ancestors';

    public const string PROP_BODY = 'body';

    public const string PROP_CONTENT = 'content';

    public const string PROP_GROUP = 'group';

    public const string PROP_ID = 'id';

    public const string PROP_KEY = 'key';

    public const string PROP_MESSAGE = 'message';

    public const string PROP_NUMBER = 'number';

    public const string PROP_REPRESENTATION = 'representation';

    public const string PROP_SPACE = 'space';

    public const string PROP_SPACE_KEY = 'spaceKey';

    public const string PROP_STATUS = 'status';

    public const string PROP_STORAGE = 'storage';

    public const string PROP_TITLE = 'title';

    public const string PROP_TYPE = 'type';

    public const string PROP_USER = 'user';

    public const string PROP_USERNAME = 'username';

    public const string PROP_VALUE = 'value';

    public const string PROP_VERSION = 'version';

    // Space Consts
    public const int SPACE_LIMIT_DEFAULT = 100;

    public const string SPACE_TYPE_GLOBAL = 'global';

    public const string SPACE_TYPE_PERSONAL = 'personal';

    // Page Consts
    public const string PAGE_COUNT = 'count';

    public const string PAGE_TYPE = 'pagetype';

    // Property Values Consts
    public const string REPRESENTATION_TYPE_STORAGE = 'storage';

    public const string STATUS_TYPE_CURRENT = 'current';

    public const int SEARCH_START = 0;

    public const int NO_SEARCH_START = -1;

    public const int SEARCH_LIMIT_ZERO = 0;

    public const int SEARCH_LIMIT_1ENTRY = 1;

    public const int NO_SEARCH_LIMIT = -1;

    public const int SEARCH_LIMIT_MAX = 100;

    public const int NO_PARENT = 0;

    public const string NO_SPACE = '';

    public const bool NO_BODY = false;

    public const string USER_TYPE_KNOWN = 'known';

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareSettings(Map $overrideParameters): void
    {
        // NothingToDo
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    final protected function validateSettings(Map $overrideParameters): bool
    {
        return true;
    }
}
