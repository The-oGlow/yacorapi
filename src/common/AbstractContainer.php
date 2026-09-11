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

use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use ollily\Tools\String\ToStringTrait;
use Psr\Log\LoggerInterface;

/**
 * Abstract implementation for a container clazz.
 * 
 * @author ollily
 */
abstract class AbstractContainer implements IContainer
{
    use ToStringTrait;

    protected ConstData $constData;

    /** @var array<mixed,mixed> Stored data */
    private array $data = [];

    /** @var int[]|string[] The modes how to access the data */
    private array $modes = [];

    private static LoggerInterface $logger;

    /**
     * Define the valid modes to access the data.
     */
    abstract protected function prepareModes(): void;

    /**
     * Initialize the data.
     */
    abstract protected function prepareData(): void;

    /**
     * Public constructor.
     */
    public function __construct()
    {
        self::$logger = new ConsoleLogger(AbstractContainer::class, level: static::LEVEL_DEFAULT);
        self::$logger->debug('START');
        // Init Dynamic Consts
        $this->constData =  new ConstData(AbstractContainer::class);
        $this->prepareModes();
        $this->prepareData();
        self::$logger->debug('END');
    }

    #[\Override]
    public function getAllData(): array
    {
        return $this->data;
    }

    /**
     * Set the complete data.
     * 
     * @param array<mixed,mixed> $allData Array of stored data
     */
    protected function setAllData(array $allData): void
    {
        $this->data = $allData;
    }

    #[\Override]
    public function getKeys(): array
    {
        return array_keys($this->getAllData());
    }

    #[\Override]
    public function keyExists(mixed $key): bool
    {
        return !empty($key) && array_key_exists($key, $this->getAllData());
    }

    #[\Override]
    public function getModes(): array
    {
        return $this->modes;
    }

    /**
     * Set all the valid modes.
     * 
     * @param int[]|string[] $modes The modes how to access the data
     */
    protected function setModes(array $modes): void
    {
        $this->modes = $modes;
    }

    #[\Override]
    public function getDataByMode(int|string $mode): mixed
    {
        self::$logger->debug('START', [$mode]);
        $value = [];
        if ($this->keyExists($mode)) {
            $value = $this->getAllData()[$mode];
            if (is_array($value) && count($value) > 20) {
                self::$logger->debug('count   :', [count($value)]);
            } else {
                self::$logger->debug('elements:', [$value]);
            }
        } else {
            self::$logger->warning('Mode not found', [$mode]);
        }
        self::$logger->debug('END');

        return $value;
    }

    #[\Override]
    protected function __toStringValues(): mixed
    {
        return $this->getAllData();
    }
}
