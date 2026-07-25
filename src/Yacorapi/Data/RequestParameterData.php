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

    // Page Consts
    public const string PAGE_COUNT = 'count';

    public const string PAGE_TYPE = 'pagetype';

    // Property Values Consts
    public const string REPRESENTATION_TYPE_STORAGE = 'storage';

    public const string STATUS_TYPE_CURRENT = 'current';

    // Search Consts
    public const int SEARCH_START = 0;

    public const int NO_SEARCH_START = -1;

    public const int SEARCH_LIMIT_ZERO = 0;

    public const int SEARCH_LIMIT_1ENTRY = 1;

    public const int SEARCH_LIMIT_MAX = 100;

    public const int NO_SEARCH_LIMIT = -1;

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
