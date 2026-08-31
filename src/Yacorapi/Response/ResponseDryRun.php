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
use Ds\Map;
use Ds\Vector;
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

    public const string DUMMY_BODY  = 'dummy-body';

    public const string DUMMY_KEY   = 'dummy-key';

    public const string DUMMY_TITLE = 'dummy-title';

    public const string DUMMY_TYPE  = 'dummy-type';

    public const string DUMMY_WEBUI = 'dummy-webui';

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
     * @inheritDoc
     */
    #[\Override]
    public function getResponse(): Map
    {
        return self::prepareResponse(true);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keyExists($key): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keys(): Vector
    {
        $map = new Map();

        return new Vector($map->keys());
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getValue(mixed $key, mixed $default = ''): mixed
    {
        return $default;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkStatus(): bool
    {
        return true;
    }

    #[\Override]
    public function getError(): Collection
    {
        return new Map();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
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
    #[\Override]
    public function getResult(int $idx): mixed
    {
        return self::dummyResultEntry(true);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function isResultsAvailable(): bool // NOSONAR: php:S4144
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkData(): bool
    {
        // TODO: Implement checkData() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkDataWrite(): mixed
    {
        // TODO: Implement checkDataWrite() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getBody(): string
    {
        // TODO: Implement method.
        return '';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getRestrictions(): array
    {
        // TODO: Implement method.
        return [];
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function __toString(): string
    {
        return self::implode_recursive(';', $this->getResponse()->toArray());
    }
}
