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

namespace oglow\tools\Yacorapi;

use Ds\Map;
use Ds\Set;

interface IResponse extends \Stringable
{
    public const KEY_ANCESTORS = 'ancestors';

    public const KEY_ARCHIVED = 'archived';

    public const KEY_BASE = 'base';

    public const KEY_BODY = 'body';

    public const KEY_CONTENT = 'content';

    public const KEY_COUNT = 'count';

    public const KEY_DESCRIPTION = 'description';

    public const KEY_GROUP = 'group';

    public const KEY_KEY = 'key';

    public const KEY_ID = 'id';

    public const KEY_LIMIT = 'limit';

    public const KEY_LINKS = '_links';

    public const KEY_MAX_RESULT = 'max-result';

    public const KEY_MESSAGE = 'message';

    public const KEY_NAME = 'name';

    public const KEY_OPERATION = 'operation';

    public const KEY_PLAIN = 'plain';

    public const KEY_READ = 'read';

    public const KEY_REASON = 'reason';

    public const KEY_RESPONSE = 'response';

    public const KEY_RESTRICTIONS = 'restrictions';

    public const KEY_RESULTS = 'results';

    public const KEY_SIZE = 'size';

    public const KEY_SPACE = 'space';

    public const KEY_SPACES = 'spaces';

    public const KEY_START = 'start';

    public const KEY_START_INDEX = 'start-index';

    public const KEY_STATUS = 'status';

    public const KEY_STATUS_CODE = 'statusCode';

    public const KEY_STORAGE = 'storage';

    public const KEY_TITLE = 'title';

    public const KEY_TOTAL = 'total';

    public const KEY_TOTAL_SIZE = 'totalSize';

    public const KEY_TYPE = 'type';

    public const KEY_UPDATE = 'update';

    public const KEY_URL = 'url';

    public const KEY_USER = 'user';

    public const KEY_VALUE = 'value';

    public const KEY_WEBUI = 'webui';

    // Messages
    public const MSG_ERROR = 'Error with Status';

    public const VAL_TRUE = 'true';

    public const VAL_FALSE = 'false';

    /**
     * @return Map<mixed,mixed>
     */
    public function getResponse(): Map;

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function keyExists(mixed $key): bool;

    /**
     * @return Set<mixed>
     */
    public function keys(): Set;

    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getValue(mixed $key, mixed $default = ''): mixed;

    /**
     * Response is correct or has an error.
     *
     * @return bool TRUE=response has no error, else FALSE
     */
    public function checkStatus(): bool;

    /**
     * Response has data.
     *
     * @return bool TRUE=response has data, else FALSE
     */
    public function checkData(): bool;

    /**
     * Data for Writing is valid.
     *
     * @return mixed pageId=Data is valid, else FALSE
     */
    public function checkDataWrite(): mixed;

    /**
     * @return Map<mixed,mixed>
     */
    public function getResults(): Map;

    /**
     * @param int $idx
     *
     * @return mixed
     */
    public function getResult(int $idx): mixed;

    /**
     * @return string
     */
    public function getBody(): string;

    /**
     * @return array<mixed,mixed>
     */
    public function getRestrictions(): array;

    /**
     * Response has results.
     *
     * @return bool TRUE=has results, else FALSE
     */
    public function isResultsAvailable(): bool;

    /**
     * @inheritDoc
     */
    public function __toString();
}
