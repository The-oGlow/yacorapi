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

use Ds\Map;
use Ds\Set;
use oglow\tools\Yacorapi\IResponse;
use ollily\Tools\String\ImplodeTrait;

/**
 * @SuppressWarnings("PMD")
 *
 * @psalm-suppress
 */
class ResponseDryRun implements IResponse
{
    use ImplodeTrait;

    public const DUMMY_BODY  = 'dummy-body';

    public const DUMMY_KEY   = 'dummy-key';

    public const DUMMY_TITLE = 'dummy-title';

    public const DUMMY_TYPE  = 'dummy-type';

    public const DUMMY_WEBUI = 'dummy-webui';

    /**
     * @return array<mixed,mixed>
     */
    protected static function dummyBody(): array
    {
        return [IResponse::KEY_STORAGE => [IResponse::KEY_VALUE => self::DUMMY_BODY]];
    }

    /**
     * @param bool $withBody
     * @param bool $withContent
     *
     * @return array<mixed,mixed>
     */
    protected static function dummyResultEntry(bool $withBody = false, bool $withContent = false): array
    {
        if ($withContent) {
            $entry                         = [];
            $entry[IResponse::KEY_CONTENT] = [
                IResponse::KEY_KEY   => self::DUMMY_KEY,
                IResponse::KEY_TITLE => self::DUMMY_TITLE,
                IResponse::KEY_TYPE  => self::DUMMY_TYPE,
                IResponse::KEY_LINKS => [IResponse::KEY_WEBUI => self::DUMMY_WEBUI],
                IResponse::KEY_SPACE => [IResponse::KEY_KEY => self::DUMMY_KEY],
            ];
            if ($withBody) {
                $entry[self::KEY_CONTENT][IResponse::KEY_BODY] = self::dummyBody();
            }
        } else {
            $entry = [
                IResponse::KEY_KEY   => self::DUMMY_KEY,
                IResponse::KEY_TITLE => self::DUMMY_TITLE,
                IResponse::KEY_TYPE  => self::DUMMY_TYPE,
                IResponse::KEY_LINKS => [IResponse::KEY_WEBUI => self::DUMMY_WEBUI],
                IResponse::KEY_SPACE => [IResponse::KEY_KEY => self::DUMMY_KEY],
            ];
            if ($withBody) {
                $entry[IResponse::KEY_BODY] = self::dummyBody();
            }
        }

        return $entry;
    }

    /**
     * @param bool $withBody
     *
     * @return Map<mixed,mixed>
     */
    public static function prepareResponse(bool $withBody = false): Map
    {
        $response = new Map();
        $response->put(self::KEY_RESULTS, [0 => self::dummyResultEntry($withBody)]);
        $response->put(self::KEY_START, 0);
        $response->put(self::KEY_SIZE, 1);
        $response->put(self::KEY_LIMIT, 22);
        $response->put(self::KEY_TOTAL_SIZE, 1);

        return $response;
    }

    /**
     * @return Map<mixed,mixed>
     */
    public function getResponse(): Map
    {
        return self::prepareResponse(true);
    }

    /**
     * @inheritDoc
     */
    public function keyExists($key): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function keys(): Set
    {
        return new Map()->keys();
    }

    /**
     * @inheritDoc
     */
    public function getValue($key, $default = '')
    {
        return $default;
    }

    /**
     * @inheritDoc
     */
    public function checkStatus(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getResults(): Map
    {
        $response = new Map();
        $response->put(
            self::KEY_RESULTS,
            [
                0 => self::dummyResultEntry(true),
                1 => self::dummyResultEntry(true),
            ]
        );

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function getResult(int $idx)
    {
        return self::dummyResultEntry(true);
    }

    /**
     * @inheritDoc
     */
    public function isResultsAvailable(): bool // NOSONAR: php:S4144
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function checkData(): bool
    {
        // TODO: Implement checkData() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function checkDataWrite()
    {
        // TODO: Implement checkDataWrite() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): string
    {
        // TODO: Implement method.
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getRestrictions(): array
    {
        // TODO: Implement method.
        return [];
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->implode_recursive(';', $this->getResponse()->toArray());
    }
}
