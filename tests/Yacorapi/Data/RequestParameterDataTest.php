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

namespace oglow\tools\Yacorapi\Data;

use PHPUnit\Framework\ConstantCheckTestCase;

class RequestParameterDataTest extends ConstantCheckTestCase
{
    protected const string CLASS_PREFIX     = RequestParameterData::class . self::C_STATIC_SEP;

    private const int   EXPECTED_PROPERTY_COUT  = 19;

    protected const int EXPECTED_CONSTANT_COUNT = 36;

    protected const bool WITH_CONST_CROSSCHECK = true;

    #[\Override]
    protected static function prepareO2t(): RequestParameterData
    {
        return new RequestParameterData();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): RequestParameterData
    {
        return $this->o2t;
    }

    #[\Override]
    public static function setUpBeforeClass(bool $withConstCrossCheck = self::WITH_CONST_CROSSCHECK, int $expectedConstsCount = self::EXPECTED_CONSTANT_COUNT): void
    {
        parent::setUpBeforeClass($withConstCrossCheck, $expectedConstsCount);
    }

    public function testConstantsOthers(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'SPACE_LIMIT_DEFAULT',
            self::CLASS_PREFIX . 'PAGE_COUNT',
            self::CLASS_PREFIX . 'PAGE_TYPE',
            self::CLASS_PREFIX . 'REPRESENTATION_TYPE_STORAGE',
            self::CLASS_PREFIX . 'SEARCH_START',
            self::CLASS_PREFIX . 'SEARCH_LIMIT_ZERO',
            self::CLASS_PREFIX . 'SEARCH_LIMIT_1ENTRY',
            self::CLASS_PREFIX . 'SEARCH_LIMIT_MAX',
            self::CLASS_PREFIX . 'STATUS_TYPE_CURRENT',
            self::CLASS_PREFIX . 'USER_TYPE_KNOWN',
            self::CLASS_PREFIX . 'NO_PARENT',
            self::CLASS_PREFIX . 'NO_BODY',
            self::CLASS_PREFIX . 'NO_SEARCH_LIMIT',
            self::CLASS_PREFIX . 'NO_SEARCH_START',
            self::CLASS_PREFIX . 'NO_SPACE',
            self::CLASS_PREFIX . 'NO_PAGE_ID',
            self::CLASS_PREFIX . 'EMPTY_BODY',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    public function testPropConstants(): void
    {
        $callback           = fn ($val, $key) => str_starts_with($key, 'PROP_');
        $const             = array_filter(self::getAllDefinedConsts(RequestParameterData::class), $callback, ARRAY_FILTER_USE_BOTH);
        $map                = fn ($val) => RequestParameterData::class . self::C_STATIC_SEP . $val;
        $const             = array_map($map, array_keys($const));
        static::updateActualConsts($const);

        self::assertCount(self::EXPECTED_PROPERTY_COUT, $const, print_r($const, true));
    }
}
