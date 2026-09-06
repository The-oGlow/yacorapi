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

namespace oglow\tools\Yacorapi\Request;

use Ds\Collection;
use oglow\tools\common\AbstractSingleton;

class RequestParameterData extends AbstractSingleton
{
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
    public const int VAL_SPACE_LIMIT_DEFAULT = 500;

    public const string VAL_SPACE_EMPTY = '';

    // Property Values Consts
    public const string VAL_REPRESENTATION_TYPE_STORAGE = 'storage';

    public const string VAL_STATUS_TYPE_CURRENT = 'current';

    // Search Consts
    public const int VAL_SEARCH_START = 0;

    public const int VAL_SEARCH_START_NO = -1;

    public const int VAL_SEARCH_LIMIT_1ENTRY = 1;

    public const int VAL_SEARCH_LIMIT_MIN = 0;

    public const int VAL_SEARCH_LIMIT_MAX = 100;

    public const int VAL_SEARCH_LIMIT_NO = -1;

    // Page Consts
    public const int VAL_PARENT_ID_NO = 0;

    public const int VAL_PAGE_ID_NO = -1;

    public const bool VAL_BODY_NO = false;

    public const string VAL_USER_TYPE_KNOWN = 'known';

    public const string VAL_PAGE_TITLE_EMPTY = '';

    public const string VAL_BODY_EMPTY = '';

    public const int VAL_VERSION_FIRST = 1;

    public const string VAL_COMMENT_EMPTY = '';

    /** Max length of a comment (without APP_USER) */
    public const int VAL_COMMENT_MAXLEN = 200;

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareSettings(Collection $overrideParameters): void
    {
        // NothingToDo
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    final protected function validateSettings(Collection $overrideParameters): bool
    {
        return true;
    }
}
