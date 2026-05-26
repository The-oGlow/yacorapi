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

namespace oglow\tools\Yacorapi;

use PHPUnit\Framework\ConstantCheckTestCase;

class ExitCodesTest extends ConstantCheckTestCase
{
    public const CLASS_PREFIX = ExitCodesTest::class . self::C_STATIC_SEP;

    /** @var int */
    protected const EXPECTED_CONSTANT_COUNT = 8;

    /** @var bool */
    protected const WITH_CONST_CROSSCHECK = true;

    /**
     * @return ExitCodes
     */
    protected static function prepareO2t()
    {
        return new ExitCodes();
    }

    /**
     * @return ExitCodes
     */
    protected function getCasto2t()
    {
        return $this->o2t;
    }

    public static function setUpBeforeClass(bool $withConstCrossCheck = self::WITH_CONST_CROSSCHECK, int $expectedConstsCount = self::EXPECTED_CONSTANT_COUNT): void
    {
        parent::setUpBeforeClass($withConstCrossCheck, $expectedConstsCount);
    }

    public function testConstsExists(): void
    {
        $const = [
            self::CLASS_PREFIX . 'ERR_CODE_NO_URL_SET',
            self::CLASS_PREFIX . 'ERR_CODE_AUTH_CLASS_NOT_EXISTS',
            self::CLASS_PREFIX . 'ERR_CODE_AUTHFILE_NOT_EXISTS',
            self::CLASS_PREFIX . 'ERR_CODE_EXTENSION_NOT_LOADED',
            self::CLASS_PREFIX . 'ERR_CODE_MYSPACES_FILE_NOT_EXISTS',
            self::CLASS_PREFIX . 'ERR_CODE_BLOCKER_ADDON_NOT_INIT',
            self::CLASS_PREFIX . 'ERR_CODE_SINGLEADDON_NOT_INIT',
            self::CLASS_PREFIX . 'ERR_CODE_ALLADDON_NOT_INIT',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }
}
