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

namespace oglow\tools\Yacorapi\Request;

use PHPUnit\Framework\ConstantCheckTestCase;

class RequestTypeEnumTest extends ConstantCheckTestCase
{
    public const string CLASS_PREFIX = RequestTypeEnum::class . self::C_STATIC_SEP;

    protected const int EXPECTED_CONSTANT_COUNT = 4;

    protected const bool WITH_CONST_CROSSCHECK = true;

    /**
     * @return RequestTypeEnum
     */
    #[\Override]
    protected static function prepareO2t(): RequestTypeEnum
    {
        return RequestTypeEnum::GET;
    }

    /**
     * @return RequestTypeEnum
     */
    #[\Override]
    protected function getCasto2t(): RequestTypeEnum
    {
        return  $this->o2t;
    }

    #[\Override]
    public static function setUpBeforeClass(bool $withConstCrossCheck = self::WITH_CONST_CROSSCHECK, int $expectedConstsCount = self::EXPECTED_CONSTANT_COUNT): void
    {
        parent::setUpBeforeClass($withConstCrossCheck, $expectedConstsCount);
    }

    public function testConstsExists(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'GET',
            self::CLASS_PREFIX . 'PUT',
            self::CLASS_PREFIX . 'DELETE',
            self::CLASS_PREFIX . 'POST',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }
}
