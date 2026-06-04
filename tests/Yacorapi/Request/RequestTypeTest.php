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

class RequestTypeTest extends ConstantCheckTestCase
{
    public const  CLASS_PREFIX = RequestType::class . self::C_STATIC_SEP;

    protected const int EXPECTED_CONSTANT_COUNT = 4;

    protected const bool WITH_CONST_CROSSCHECK = true;

    /**
     * @return RequestType
     */
    protected static function prepareO2t(): RequestType
    {
        return new RequestType();
    }

    /**
     * @return RequestType
     */
    protected function getCasto2t(): RequestType
    {
        return  $this->o2t;
    }

    public static function setUpBeforeClass(bool $withConstCrossCheck = self::WITH_CONST_CROSSCHECK, int $expectedConstsCount = self::EXPECTED_CONSTANT_COUNT): void
    {
        parent::setUpBeforeClass($withConstCrossCheck, $expectedConstsCount);
    }

    public function testConstsExists(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'REQ_TYP_GET',
            self::CLASS_PREFIX . 'REQ_TYP_PUT',
            self::CLASS_PREFIX . 'REQ_TYP_DELETE',
            self::CLASS_PREFIX . 'REQ_TYP_POST',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }
}
