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

    protected const int EXPECTED_CONSTANT_COUNT = 43;

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

    public function testConstantsExpand(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'REQP_SPACE_LIST',
            self::CLASS_PREFIX . 'REQP_FULL',
            self::CLASS_PREFIX . 'REQP_SEARCH_FULL',
            self::CLASS_PREFIX . 'REQP_LIGHT',
            self::CLASS_PREFIX . 'REQP_PERM',
            self::CLASS_PREFIX . 'REQP_SEARCH_LIGHT',
            self::CLASS_PREFIX . 'REQP_RESTRICTIONS_FULL',
            self::CLASS_PREFIX . 'RESP_CSV_SPACE_RESULTS',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    public function testConstantsPage(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'ITEM_TYPES'     => 3,
            self::CLASS_PREFIX . 'ITEM_TYPES_ALL' => 4,
        ];
        static::updateActualConsts(array_keys($const));

        $this->verifyConstArrayAllExists($const);

        $const              = [
            self::CLASS_PREFIX . 'SPACE_LIMIT_DEFAULT',
            self::CLASS_PREFIX . 'SPACE_TYPE_GLOBAL',
            self::CLASS_PREFIX . 'SPACE_TYPE_PERSONAL',
            self::CLASS_PREFIX . 'ITEM_TYPE_PAGE',
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
