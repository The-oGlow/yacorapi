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

class ConstDataTest extends ConstantCheckTestCase
{
    public const string  CLASS_PREFIX = ConstData::class . self::C_STATIC_SEP;

    protected const int EXPECTED_CONSTANT_COUNT = 41;

    protected const bool WITH_CONST_CROSSCHECK = true;

    #[\Override]
    protected static function prepareO2t(): ConstData
    {
        return new ConstData();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): ConstData
    {
        return $this->o2t;
    }

    #[\Override]
    public static function setUpBeforeClass(bool $withConstCrossCheck = self::WITH_CONST_CROSSCHECK, int $expectedConstsCount = self::EXPECTED_CONSTANT_COUNT): void
    {
        parent::setUpBeforeClass($withConstCrossCheck, $expectedConstsCount);
    }

    public function testGlobalConstsExists(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'KEY_MY_DIR',
            self::CLASS_PREFIX . 'VAL_APP_USER',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    public function testPathConstsExists(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'KEY_PROJECT_ROOT',
            self::CLASS_PREFIX . 'KEY_TARGET_ROOTDIR',
            self::CLASS_PREFIX . 'KEY_TARGET_DIR',
            self::CLASS_PREFIX . 'KEY_INPUT_ROOTDIR',
            self::CLASS_PREFIX . 'KEY_INPUT_DIR',
            self::CLASS_PREFIX . 'TARGET_ORGDIR',
            self::CLASS_PREFIX . 'TARGET_MODDIR',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    public function testClassConstsExists(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'PAGE_START',
            self::CLASS_PREFIX . 'PAGE_LIMIT',
            self::CLASS_PREFIX . 'PAGE_MAX_PAGES',
            self::CLASS_PREFIX . 'PAGE_MAX_RESULTS',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    public function testCliParameterSize(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'CLI_LONG_OPTS' => 1,
        ];
        static::updateActualConsts(array_keys($const));

        $this->verifyConstArrayAllExists($const);
    }

    public function testConfigConstsExists(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'C_RAPI_CONTENT',
            self::CLASS_PREFIX . 'C_RAPI_SCAN',
            self::CLASS_PREFIX . 'C_RAPI_SEARCH',
            self::CLASS_PREFIX . 'C_RAPI_SPACE',
            self::CLASS_PREFIX . 'C_RAPI_VIEWPAGE',
            self::CLASS_PREFIX . 'C_RAPI_RESTRICTION_BYOP',
            self::CLASS_PREFIX . 'C_RAPI_RESTRICTION',
            self::CLASS_PREFIX . 'CONF_USERCERTFILE',
            self::CLASS_PREFIX . 'CONF_USERAUTHFILE',
            self::CLASS_PREFIX . 'CONF_USERFOLDER',
            self::CLASS_PREFIX . 'CONF_AUTH_CLAZZ',
            self::CLASS_PREFIX . 'ENV_HOME',
            self::CLASS_PREFIX . 'ENV_OFFSET',
            self::CLASS_PREFIX . 'ENV_USERPROFILE',
            self::CLASS_PREFIX . 'KEY_CONF_PAT_PROD',
            self::CLASS_PREFIX . 'KEY_CONF_PAT_TEST',
            self::CLASS_PREFIX . 'KEY_USE_PROD',
            self::CLASS_PREFIX . 'KEY_TEST_URL',
            self::CLASS_PREFIX . 'KEY_PROD_URL',
            self::CLASS_PREFIX . 'KEY_CONF_BASE_URL',
            self::CLASS_PREFIX . 'KEY_AUTH_TOKEN_NAME',
            self::CLASS_PREFIX . 'KEY_MY_CERT_CA',
            self::CLASS_PREFIX . 'KEY_CONF_CONTENT_URL',
            self::CLASS_PREFIX . 'KEY_CONF_SEARCH_URL',
            self::CLASS_PREFIX . 'KEY_CONF_SPACE_URL',
            self::CLASS_PREFIX . 'KEY_WEB_SHOW_PAGEID',
            self::CLASS_PREFIX . 'KEY_SEARCH_LIMIT',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }
}
