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

namespace oglow\tools\common;

use Psr\Log\LogLevel;
use Stringable;
/**
 * Interface for a helper clazz.
 * 
 * @author olliy
 */
interface IContainer extends Stringable
{
    /** @var LogLevel Default output level */
    public const string LEVEL_DEFAULT = LogLevel::INFO;

    /**
     * Returns all store data in the container.
     * 
     * @return array<mixed,mixed> Array of stored data
     */
    public function getAllData(): array;

    /**
     * Returns all keys used in the container.
     * 
     * @return array<mixed,mixed> Array of keys
     */
    public function getKeys(): array;

    /**
     * Checks, if the key is used in the container.
     * 
     * @param mixed $key Name of the key
     *
     * @return bool TRUE=the key exists, else FALSE
     */
    public function keyExists(mixed $key): bool;

    /**
     * Returns the modes how to access the assigned data for this mode in the container.
     * 
     * @return int[]|string[] Array of modes
     */
    public function getModes(): array;

    /**
     * Return the assigned data for this mode.
     * 
     * @param int|string $mode The mode to access the assigned data
     *
     * @return mixed The assigned data or null
     */
    public function getDataByMode(int|string $mode): mixed;

    /**
     * @inheritDoc
     */
    #[\Override]
    public function __toString(): string;
}
