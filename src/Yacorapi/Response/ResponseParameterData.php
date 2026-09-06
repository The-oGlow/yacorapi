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

namespace oglow\tools\Yacorapi\Response;

use Ds\Collection;
use oglow\tools\common\AbstractSingleton;

class ResponseParameterData extends AbstractSingleton
{
    // Access Keys

    public const string KEY_ANCESTORS = 'ancestors';

    public const string KEY_ARCHIVED = 'archived';

    public const string KEY_BASE = 'base';

    public const string KEY_BODY = 'body';

    public const string KEY_CONTENT = 'content';

    public const string KEY_COUNT = 'count';

    public const string KEY_DESCRIPTION = 'description';

    public const string KEY_GROUP = 'group';

    public const string KEY_KEY = 'key';

    public const string KEY_ID = 'id';

    public const string KEY_LIMIT = 'limit';

    public const string KEY_LINKS = '_links';

    public const string KEY_MAX_RESULT = 'max-result';

    public const string KEY_MESSAGE = 'message';

    public const string KEY_NAME = 'name';

    public const string KEY_NUMBER = 'number';

    public const string KEY_OPERATION = 'operation';

    public const string KEY_PLAIN = 'plain';

    public const string KEY_READ = 'read';

    public const string KEY_REASON = 'reason';

    public const string KEY_RESPONSE = 'response';

    public const string KEY_RESTRICTIONS = 'restrictions';

    public const string KEY_RESULTS = 'results';

    public const string KEY_SIZE = 'size';

    public const string KEY_SPACE = 'space';

    public const string KEY_SPACES = 'spaces';

    public const string KEY_START = 'start';

    public const string KEY_START_INDEX = 'start-index';

    public const string KEY_STATUS = 'status';

    public const string KEY_STATUS_CODE = 'statusCode';

    public const string KEY_STORAGE = 'storage';

    public const string KEY_TITLE = 'title';

    public const string KEY_TOTAL = 'total';

    public const string KEY_TOTAL_SIZE = 'totalSize';

    public const string KEY_TYPE = 'type';

    public const string KEY_UPDATE = 'update';

    public const string KEY_URL = 'url';

    public const string KEY_USER = 'user';

    public const string KEY_VALUE = 'value';

    public const string KEY_VERSION = 'version';

    public const string KEY_WEBUI = 'webui';

    public const string KEY_HOMEPAGE = 'homepage';

    // Messages
    public const string ERR_MSG_COMMON = 'Error with Status';

    // Values
    public const string VAL_TRUE = 'true';

    public const string VAL_FALSE = 'false';

    public const string VAL_BODY_EMPTY = '';

    public const int VAL_PAGE_ID_NO = -1;

    public const int VAL_VERSION_NO = -1;

    public const string VAL_TITLE_EMPTY = '';

    public const int VAL_RESULT_FIRST = 0;

    #[\Override]
    protected function prepareSettings(Collection $overrideParameters): void
    {
    }

    #[\Override]
    protected function validateSettings(Collection $overrideParameters): bool
    {
        return true;
    }
}
