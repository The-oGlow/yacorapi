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
use Psr\Log\LogLevel;

/**
 * Abstract implementation for a singleton.
 *
 * @author olliy
 *
 * @psalm-consistent-constructor
 */
abstract class AbstractSingleton implements ISingleton
{
    /** @var array<object> the real instance of the singleton */
    private static array $instance = [];

    private static LoggerInterface $logger;

    /**
     * Protected constructor.
     *
     * @param bool                   $withLogger TRUE=activate logging, else FALSE
     * @param int|LogLevel::*|string $level      The minimum logging level at which this handler will be triggered (Default: {@link self::LEVEL_DEFAULT})
     */
    protected function __construct(bool $withLogger = true, LogLevel|string|int $level = self::LEVEL_DEFAULT)
    {
        if ($withLogger) {
            /** @psalm-suppress ArgumentTypeCoercion
             * @phpstan-ignore argument.type */
            self::$logger = new ConsoleLogger(AbstractSingleton::class, level: $level);
        } else {
            self::$logger = new DoNothingLogger();
        }
        self::$logger->debug('START');

        $overrideParameters = self::parseArguments($this->prepareShortOpts(), $this->prepareLongOpts());
        $this->prepareSettings($overrideParameters);
        $valid = $this->validateSettings($overrideParameters);

        self::$logger->debug('END - Is initiated', [$valid]);
    }

    /**
     * Returns static access on this singletion.
     *
     * @param bool                   $withLogger TRUE=activate logging, else FALSE
     * @param int|LogLevel::*|string $level      The minimum logging level at which this handler will be triggered (Default: {@link ISingleton::LEVEL_DEFAULT})
     *
     * @return object This singleton
     *
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    public static function i(bool $withLogger = true, LogLevel|string|int $level = ISingleton::LEVEL_DEFAULT): object
    {
        $key = static::class;
        if (!array_key_exists($key, self::$instance)) {
            self::$instance[$key] = new static($withLogger, $level);
        }

        return self::$instance[$key];
    }

    /**
     * Returns the unique id of this singleton.
     *
     * @return string The unique id of this singleton
     */
    public static function getKey(): string
    {
        return array_key_exists(static::class, self::$instance) ? static::class : '';
    }

    /**
     * Returns a boolean value from a settings entry (incl. overrides).
     *
     * @param Collection<mixed, mixed> $overrideParameters Override the settings with these parameters
     * @param string                   $keyName            The id of a bool parameter
     *
     * @return mixed The boolean value of the parameter or ''
     */
    protected function parseBool(Collection $overrideParameters, string $keyName): mixed
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

    /**
     * Parse the given override parameters.
     *
     * @param string       $shortOpts Override parameter as short version
     * @param array<mixed> $longOpts  Override parameter as long version
     *
     * @return Collection<mixed, mixed> A collection of override parameter
     */
    private static function parseArguments(string $shortOpts, array $longOpts): Collection
    {
        /** @var Map<mixed,mixed> */
        $mapOpts = new Map();
        $opts = getopt($shortOpts, $longOpts);
        if (is_array($opts)) {
            $mapOpts = new Map($opts);
        }

        return $mapOpts;
    }

    /**
     * Initialize this singleton with the settings.
     *
     * @param Collection<mixed, mixed> $overrideParameters Override the settings with these parameters
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function prepareSettings(Collection $overrideParameters): void
    {
        // nothing to do here
    }

    /**
     * Check, if the settings are valid.
     *
     * @param Collection<mixed, mixed> $overrideParameters Verify the settings with these parameters
     *
     * @return bool TRUE=settings are valid, else FALSE
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function validateSettings(Collection $overrideParameters): bool
    {
        return true;
    }

    /**
     * Define the valid short options.
     *
     * @return string The short options
     */
    protected function prepareShortOpts(): string
    {
        return '';
    }

    /**
     * Define the valid log options.
     *
     * @return array<mixed> Array of long options
     */
    protected function prepareLongOpts(): array
    {
        return [];
    }

    private function __clone()
    {
        // nothing to do here
    }
}
