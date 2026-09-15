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
 * Response which is used as mock for a dry run.
 *
 * @author ollily
 */
class ResponseDryRun implements IResponse {

    use ImplodeTrait;

    public const string DUMMY_BODY = 'dummy-body';
    public const int DUMMY_ID = 9999;
    public const string DUMMY_KEY = 'dummy-key';
    public const string DUMMY_TITLE = 'dummy-title';
    public const string DUMMY_DESCR = 'dummy-description';
    public const string DUMMY_TYPE = 'dummy-type';
    public const string DUMMY_STATUS = 'dummy-status';
    public const string DUMMY_WEBUI = 'dummy-webui';
    public const int VAL_RESULT_START = 0;
    public const int VAL_RESULT_SIZE = 1;
    public const int VAL_RESULT_LIMIT = 20;
    public const int VAL_RESULT_TOTAL_SIZE = 1;

    /**
     * @return array<mixed,mixed>
     */
    protected static function dummyBody(): array {
        return [ResponseParameter::KEY_STORAGE => [ResponseParameter::KEY_VALUE => self::DUMMY_BODY]];
    }

    /**
     * @param bool $withBody
     * @param bool $isContentArray
     *
     * @return array<mixed,mixed>
     */
    protected static function dummyResultEntry(bool $withBody = false, bool $isContentArray = false): array {
        $item = [
            ResponseParameter::KEY_ID => self::DUMMY_ID,
            ResponseParameter::KEY_KEY => self::DUMMY_KEY,
            ResponseParameter::KEY_TITLE => self::DUMMY_TITLE,
            ResponseParameter::KEY_TYPE => self::DUMMY_TYPE,
            ResponseParameter::KEY_STATUS => self::DUMMY_STATUS,
            ResponseParameter::KEY_LINKS => [ResponseParameter::KEY_WEBUI => self::DUMMY_WEBUI],
            ResponseParameter::KEY_SPACE => [ResponseParameter::KEY_KEY => self::DUMMY_KEY],
        ];
        if ($withBody) {
            $item[ResponseParameter::KEY_BODY][ResponseParameter::KEY_STORAGE][ResponseParameter::KEY_VALUE] = self::dummyBody();
        }

        $entry = [];
        if ($isContentArray) {
            $entry[ResponseParameter::KEY_CONTENT] = $item;
        } else {
            $entry = $item;
        }

        return $entry;
    }

    /**
     * @param bool $withBody
     *
     * @return Collection<mixed,mixed>
     *
     * @phpstan-return Map<mixed,mixed>
     */
    public static function prepareResponse(bool $withBody = false): Collection {
        $response = new Map();
        $response->put(ResponseParameter::KEY_RESULTS, [self::VAL_RESULT_START => self::dummyResultEntry($withBody)]);
        $response->put(ResponseParameter::KEY_START, self::VAL_RESULT_START);
        $response->put(ResponseParameter::KEY_SIZE, self::VAL_RESULT_SIZE);
        $response->put(ResponseParameter::KEY_LIMIT, self::VAL_RESULT_LIMIT);
        $response->put(ResponseParameter::KEY_TOTAL_SIZE, self::VAL_RESULT_TOTAL_SIZE);

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getRawData(): Collection {
        return self::prepareResponse(true);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keyExists($key): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keys(): Vector {
        $map = new Map();

        return new Vector($map->keys());
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getValue(mixed $key, mixed $default = ''): mixed {
        return $default;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkStatus(): bool {
        return true;
    }

    #[\Override]
    public function getError(): Collection {
        return new Map();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getResults(): Collection {
        $response = new Map();
        $response->put(
                ResponseParameter::KEY_RESULTS,
                [
                    self::VAL_RESULT_START => self::dummyResultEntry(true),
                    (self::VAL_RESULT_START + 1) => self::dummyResultEntry(true),
                ]
        );

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getResultsCount(): int {
        return $this->getResults()->count();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getResult(int $idx): mixed {
        return self::dummyResultEntry(true);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function isResultsAvailable(): bool { // NOSONAR: php:S4144 
        return true;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkData(): bool {
        // TODO: Implement checkData() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkDataWrite(): mixed {
        // TODO: Implement checkDataWrite() method.
        return false;
    }

    #[\Override]
    public function getItemId(): int {
        return self::DUMMY_ID;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getBody(): string {
        // TODO: Implement method.
        return '';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getRestrictions(): array {
        // TODO: Implement method.
        return [];
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function __toString(): string {
        return self::implode_recursive(';', $this->getRawData()->toArray());
    }
}
