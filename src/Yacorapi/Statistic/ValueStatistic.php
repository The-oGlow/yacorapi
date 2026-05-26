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

namespace oglow\tools\Yacorapi\Statistic;

use Ds\Set;

class ValueStatistic extends AbstractStatistic
{
    public const    C_STATISTIC_NAME = 'statisticName';

    public const    C_EXPORT_NAME1   = 'exportName';

    public const    C_VALUE          = 'value';

    protected const EXPORT_NAME = self::C_VALUE;

    /** @var Set<mixed> */
    private $keysForValue;

    /** @var mixed */
    private $value;

    /**
     * ValueStatistic constructor.
     *
     * @param string $statisticName
     */
    public function __construct($statisticName)
    {
        parent::__construct($statisticName);
        $this->keysForValue = new Set([self::EXPORT_NAME]);
    }

    /**
     * @return Set<mixed>
     */
    public function keys(): Set
    {
        return $this->keysForValue;
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function keyExists($key): bool
    {
        return $this->keysForValue->contains($key);
    }

    /**
     * @param mixed      $key
     * @param IStatistic $item
     *
     * @see addValue()
     */
    public function addItem($key, $item): void
    {
        throw new \BadMethodCallException('Use instead \'->addValue\'');
    }

    /**
     * @param mixed $key
     *
     * @return null|IStatistic
     *
     * @see getValue()
     */
    public function getItem($key)
    {
        throw new \BadMethodCallException('Use instead \'->getValue\'');
    }

    /**
     * @param mixed $item
     */
    public function addValue($item): void
    {
        $this->value = $item;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    protected function __toStringValues(): array
    {
        return [
            self::C_STATISTIC_NAME => $this->getStatisticName(),
            self::C_EXPORT_NAME1   => $this->getExportName(),
            self::C_VALUE          => $this->value
        ];
    }
}
