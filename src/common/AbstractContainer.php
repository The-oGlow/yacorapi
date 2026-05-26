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

abstract class AbstractContainer implements IContainer
{
    use ToStringTrait;

    /** @var ConstData */
    protected $constData;

    /** @var mixed[] */
    private $data = [];

    /** @var int[]|string[] */
    private $modes = [];

    /** @var LoggerInterface */
    private static $logger;

    abstract protected function prepareModes(): void;

    abstract protected function prepareData(): void;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(AbstractContainer::class);
        self::$logger->debug('START');
        // Init Dynamic Consts
        $this->constData =  new ConstData(AbstractContainer::class);
        $this->prepareModes();
        $this->prepareData();
        self::$logger->debug('END');
    }

    /**
     * @return mixed[]
     */
    public function getAllData(): array
    {
        return $this->data;
    }

    /**
     * @param mixed[] $allData
     */
    protected function setAllData(array $allData): void
    {
        $this->data = $allData;
    }

    /**
     * @return mixed[]
     */
    public function getKeys(): array
    {
        return array_keys($this->getAllData());
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function keyExists($key): bool
    {
        return !empty($key) && array_key_exists($key, $this->getAllData());
    }

    /**
     * @return int[]|string[]
     */
    public function getModes(): array
    {
        return $this->modes;
    }

    /**
     * @param int[]|string[] $modes
     */
    protected function setModes(array $modes): void
    {
        $this->modes = $modes;
    }

    /**
     * @param int|string $mode
     *
     * @return mixed
     */
    public function getDataByMode($mode)
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

    /**
     * @return mixed[]
     *
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    protected function __toStringValues(): array
    {
        return $this->getAllData();
    }
}
