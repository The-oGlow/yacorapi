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
            self::CLASS_PREFIX . 'VAL_BODY_EMPTY',
            self::CLASS_PREFIX . 'VAL_BODY_NO',
            self::CLASS_PREFIX . 'VAL_COMMENT_MAXLEN',
            self::CLASS_PREFIX . 'VAL_PAGE_ID_NO',
            self::CLASS_PREFIX . 'VAL_PARENT_ID_NO',
            self::CLASS_PREFIX . 'VAL_REPRESENTATION_TYPE_STORAGE',
            self::CLASS_PREFIX . 'VAL_SEARCH_LIMIT_1ENTRY',
            self::CLASS_PREFIX . 'VAL_SEARCH_LIMIT_MIN',
            self::CLASS_PREFIX . 'VAL_SEARCH_LIMIT_MAX',
            self::CLASS_PREFIX . 'VAL_SEARCH_LIMIT_NO',
            self::CLASS_PREFIX . 'VAL_SEARCH_START',
            self::CLASS_PREFIX . 'VAL_SEARCH_START_NO',
            self::CLASS_PREFIX . 'VAL_SPACE_EMPTY',
            self::CLASS_PREFIX . 'VAL_SPACE_LIMIT_DEFAULT',
            self::CLASS_PREFIX . 'VAL_STATUS_TYPE_CURRENT',
            self::CLASS_PREFIX . 'VAL_USER_TYPE_KNOWN',
            self::CLASS_PREFIX . 'VAL_VERSION_FIRST',
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
