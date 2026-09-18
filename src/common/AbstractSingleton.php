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
 * @phpstan-consistent-constructor
 */
abstract class AbstractSingleton implements ISingleton
{
    /** @var object the real instance of the singleton */
    private static ?object $instance = null;
    
    private static LoggerInterface $logger;

    /** @var string identifier for the singleton */
    private string $key = '';

    /**
     * Public constructor.
     *
     * @param string $key        Unique id of this singleton
     * @param bool   $withLogger TRUE=activate logging, else FALSE
     * @param LogLevel::*|string|int $level      The minimum logging level at which this handler will be triggered (Default: {@link AbstractEasyGoingLogger::LEVEL_DEFAULT})
     */
    private function __construct(string $key, bool $withLogger = true, LogLevel|string|int $level = self::LEVEL_DEFAULT)
    {
        if ($withLogger) {
            self::$logger = new ConsoleLogger(AbstractSingleton::class, level: $level);
        } else {
            self::$logger = new DoNothingLogger();
        }
        self::$logger->debug('START');

        if (empty($key)) {
            $key = static::class;
        }
        $overrideParameters = self::parseArguments($this->prepareShortOpts(), $this->prepareLongOpts());
        $this->key = $key;
        $this->prepareSettings($overrideParameters);
        $valid = $this->validateSettings($overrideParameters);

        self::$logger->debug('END - Is initiated', [$valid]);
    }

    /**
     * Returns static access on this singletion.
     *
     * @return object This singleton
     */
    public static function i(string $key = '', bool $withLogger = true, int|string|Level $level = self::LEVEL_DEFAULT): object
    {   
        if (self::$instance==null) {
            self::$instance = new static($key, $withLogger, $level);
        }
        return self::$instance;
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
     * @param string             $shortOpts Override parameter as short version
     * @param array<mixed,mixed> $longOpts  Override parameter as long version
     *
     * @return Collection<mixed, mixed> A collection of override parameter
     */
    private static function parseArguments(string $shortOpts, array $longOpts): Collection
    {
        return new Map(getopt($shortOpts, $longOpts));
    }

    /**
     * Returns the unique id of this singleton.
     *
     * @return string The unique idd of this singleton
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Initialize this singleton with the settings.
     *
     * @param Collection<mixed, mixed> $overrideParameters Override the settings with these parameters
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
     * @return array<mixed,mixed> Array of long options
     */
    protected function prepareLongOpts(): array
    {
        return [];
    }
    
    private function __clone()
    {
        // nothing to do here
    }

    private function __wakeup()
    {
        // nothing to do here
    }
}
