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

namespace oglow\tools\Yacorapi\Store;

use PHPUnit\Framework\ConstantCheckTestCase;

class StoreParameterTest extends ConstantCheckTestCase
{
    protected const string CLASS_PREFIX = StoreParameter::class . self::C_STATIC_SEP;

    private const int EXPECTED_OTHER_COUNT = 4 + 9 + 10;

    protected const int EXPECTED_CONSTANT_COUNT = 1 + 24;

    protected const bool WITH_CONST_CROSSCHECK = true;

    #[\Override]
    protected static function prepareO2t(): StoreParameter
    {
        return StoreParameter::i();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): StoreParameter
    {
        return $this->o2t;
    }

    #[\Override]
    public static function setUpBeforeClass(bool $withConstCrossCheck = self::WITH_CONST_CROSSCHECK, int $expectedConstsCount = self::EXPECTED_CONSTANT_COUNT): void
    {
        parent::setUpBeforeClass($withConstCrossCheck, $expectedConstsCount);
    }

    public function testConstsInheritated(): void
    {
        $const = [
            self::CLASS_PREFIX . 'LEVEL_DEFAULT',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    public function testConstants(): void
    {
        $const = [
            self::CLASS_PREFIX . 'ERR_NOT_INVOKED',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    public function testConstantsOther(): void
    {
        $const = static::filterConsts('KEY_', StoreParameter::class);
        $const = array_merge($const, static::filterConsts('C_', StoreParameter::class));
        $const = array_merge($const, static::filterConsts('DEFAULT_', StoreParameter::class));

        static::updateActualConsts($const);

        self::assertCount(self::EXPECTED_OTHER_COUNT, $const, print_r($const, true));
    }
}
