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

use Ds\Collection;
use Ds\Map;
use Ds\Vector;
use Stringable;

/**
 * Structure for holding the data which comes from a REST-API call.
 * This can be
 * <ul>
 * <li>The data of a confluence item (eg. page)</li>
 * <li>The result of search call</li>
 * <ul>.
 *
 * @author ollily
 */
interface IResponse extends Stringable
{
    /**
     * Returns the raw data of the response as it was returnd from the REST API call.
     *
     * @return Collection<mixed,mixed> The raw response
     *
     * @phpstan-return Map<mixed,mixed>
     */
    public function getRawData(): Collection;

    /**
     * Verifies, if the key does exists at the first level of the response.
     *
     * @param mixed $key The key to check for
     *
     * @return bool TRUE=true key exists, else FALSE
     */
    public function keyExists(mixed $key): bool;

    /**
     * Returns all keys at first level of the response.
     *
     * @return Vector<mixed> All keys
     */
    public function keys(): Vector;

    /**
     * Returns the value for the key (only from first level of the response) or a default value.
     *
     * @param mixed $key     The key to check for
     * @param mixed $default A default value (Default: ''=
     *
     * @return mixed The found value or {@link $default}
     */
    public function getValue(mixed $key, mixed $default = ''): mixed;

    /**
     * Response is correct or has an error.
     *
     * @return bool TRUE=response has no error, else FALSE
     */
    public function checkStatus(): bool;

    /**
     * Returns the information about the error which was produced by the last REST-API call.
     *
     * @return Collection<mixed,mixed> Error information
     *
     * @phpstan-return Map<mixed,mixed>
     */
    public function getError(): Collection;

    /**
     * Verifies if the response has data.
     *
     * @return bool TRUE=response has data, else FALSE
     */
    public function checkData(): bool;

    /**
     * Verifies if the data is valid to write.
     *
     * @return mixed itemId=Data is valid, else FALSE
     */
    public function checkDataWrite(): mixed;

    /**
     * Returns the complete search result.
     *
     * @return Collection<mixed,mixed> The complete search result
     *
     * @phpstan-return Map<mixed,mixed>
     */
    public function getResults(): Collection;

    /**
     * Returns a single result from the given position.
     *
     * @param int $idx The position in the result
     *
     * @return mixed The search result at position {@link $idx} or null
     */
    public function getResult(int $idx): mixed;

    /**
     * Returns the id of the confluence item.
     *
     * @return int The itemId
     */
    public function getItemId(): int;

    /**
     * Returns the body of the item in storage format.
     *
     * @return string The body of the item
     */
    public function getBody(): string;

    /**
     * Returns all item restrictions.
     *
     * @return array<mixed,mixed> All defined item restrictions
     */
    public function getRestrictions(): array;

    /**
     * Verifies if the search result has entries.
     *
     * @return bool TRUE=search result has at least one entry, else FALSE
     */
    public function isResultsAvailable(): bool;

    /**
     * @inheritDoc
     */
    #[\Override]
    public function __toString(): string;
}
