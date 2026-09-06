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

use Ds\Collection;
use Ds\Map;
use Monolog\ConsoleLogger;
use Monolog\DoNothingLogger;
use Psr\Log\LoggerInterface;

abstract class AbstractSingleton implements ISingleton
{
    private static LoggerInterface $logger;

    private string $key;

    /**
     * Initialize this singleton.
     *
     * @param Collection<mixed, mixed> $overrideParameters
     */
    abstract protected function prepareSettings(Collection $overrideParameters): void;

    /**
     * @param Collection<mixed, mixed> $overrideParameters
     *
     * @return bool
     */
    abstract protected function validateSettings(Collection $overrideParameters): bool;

    /**
     * @param Collection<mixed, mixed> $overrideParameters
     * @param string                   $keyName
     *
     * @return mixed
     */
    protected static function parseBool(Collection $overrideParameters, string $keyName): mixed
    {
        /** @var mixed */
        $foundBool = '';
        if (array_key_exists($keyName, $overrideParameters->toArray())) {
            $foundBool = $overrideParameters->toArray()[$keyName];
        }
        if ('' !== $foundBool) {
            $foundBool = filter_var($foundBool, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return $foundBool;
    }

    public function __construct(string $key = '', bool $withLogger = true)
    {
        if ($withLogger) {
            self::$logger = new ConsoleLogger(AbstractSingleton::class);
        } else {
            self::$logger = new DoNothingLogger();
        }
        self::$logger->debug('START');

        if (empty($key)) {
            $key = static::class;
        }
        $overrideParameters = $this->parseArguments($this->prepareShortOpts(), $this->prepareLongOpts());
        $this->key          = $key;
        $this->prepareSettings($overrideParameters);
        $valid = $this->validateSettings($overrideParameters);

        self::$logger->debug('END - Is initiated', [$valid]);
    }

    /**
     * @return string
     */
    final public function getKey(): string
    {
        return $this->key;
    }

    protected function prepareShortOpts(): string
    {
        return '';
    }

    /**
     * @return array<mixed,mixed>
     */
    protected function prepareLongOpts(): array
    {
        return [];
    }

    /**
     * @param string             $shortOpts
     * @param array<mixed,mixed> $longOpts
     *
     * @return Collection<mixed, mixed>
     */
    private function parseArguments(string $shortOpts, array $longOpts): Collection
    {
        return new Map(getopt($shortOpts, $longOpts));
    }
}
