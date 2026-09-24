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

namespace oglow\tools\Yacorapi\Response;

use PHPUnit\Framework\ConstantCheckTestCase;

class ResponseParameterTest extends ConstantCheckTestCase
{
    protected const string CLASS_PREFIX = ResponseParameter::class . self::C_STATIC_SEP;

    private const int EXPECTED_OTHER_COUNT = 45 + 13;

    protected const int EXPECTED_CONSTANT_COUNT = 1 + 4 + self::EXPECTED_OTHER_COUNT;

    protected const bool WITH_CONST_CROSSCHECK = true;

    #[\Override]
    protected static function prepareO2t(): object
    {
        return ResponseParameter::i();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): ResponseParameter
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
            self::CLASS_PREFIX . 'ERR_MSG_COMMON',
            self::CLASS_PREFIX . 'EXPORT_PAGE_MIN',
            self::CLASS_PREFIX . 'EXPORT_PAGE_LIGHT',
            self::CLASS_PREFIX . 'EXPORT_PAGE_FULL',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    public function testConstantsOther(): void
    {
        $const = static::filterConsts('KEY_', ResponseParameter::class);
        $const = array_merge($const, static::filterConsts('VAL_', ResponseParameter::class));

        static::updateActualConsts($const);

        self::assertCount(self::EXPECTED_OTHER_COUNT, $const, print_r($const, true));
    }
}
