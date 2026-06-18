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

class ValueStatistic extends AbstractStatistic {

    public const string EXPORT_NAME = 'count';

//    private mixed $value = null;

//    /**
//     * @inheritDoc
//     */
//    #[\Override]
//    public function keys(): Set {
//        return new Set([self::EXPORT_NAME]);
//    }
//
//    /**
//     * @inheritDoc
//     */
//    #[\Override]
//    public function keyExists(mixed $key): bool {
//        return $key === self::EXPORT_NAME;
//    }
//
//    /**
//     * @inheritDoc
//     */
//    #[\Override]
//    public function addItem(mixed $key, IStatistic $item): void {
//        throw new \BadMethodCallException('Use instead \'->addValue\'');
//    }
//
//    /**
//     * @inheritDoc
//     */
//    #[\Override]
//    public function getItem(mixed $key): ?IStatistic {
//        throw new \BadMethodCallException('Use instead \'->getValue\'');
//    }
//
//    /**
//     * @param mixed $item
//     */
//    public function addValue(mixed $item): void {
//        $this->value = $item;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getValue(): mixed {
//        return $this->value;
//    }

    public function header(): array {
        return [$this->getExportName()];
    }
            
    /**
     * @inheritDoc
     *
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    #[\Override]
    protected function __toStringValues(): mixed {
        return [
            $this->getExportName() => $this->getItem(self::KEY_COUNT),
        ];
    }
}
