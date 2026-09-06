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

interface IResponse extends \Stringable
{
    /**
     * @return Collection<mixed,mixed>
     *
     * @phpstan-return Map<mixed,mixed>
     */
    public function getRawData(): Collection;

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function keyExists(mixed $key): bool;

    /**
     * @return Vector<mixed>
     */
    public function keys(): Vector;

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
     * @return Collection<mixed,mixed> Error information
     *
     * @phpstan-return Map<mixed,mixed>
     */
    public function getError(): Collection;

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
     * @return Collection<mixed,mixed>
     *
     * @phpstan-return Map<mixed,mixed>
     */
    public function getResults(): Collection;

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
    #[\Override]
    public function __toString(): string;
}
