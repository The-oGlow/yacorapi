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

namespace oglow\tools\Yacorapi\Helper;

use Ds\Map;
use oglow\tools\Yacorapi\Macro\HasMacroBodyEnum;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\ConstantCheckTestCase;

class ContentHelperTest extends ConstantCheckTestCase
{
    public const string CLASS_PREFIX = ContentHelperTest::class . self::C_STATIC_SEP;

    public const string MACRO_HTML = 'html';

    public const string MACRO_CODE = 'code';

    public const string MACRO_SECTION = 'section';

    public const string MACRO_COLUMN = 'column';

    protected const int EXPECTED_CONSTANT_COUNT = 8;

    protected const bool WITH_CONST_CROSSCHECK = true;

    /**
     * @return ContentHelper
     */
    #[\Override]
    protected static function prepareO2t(): ContentHelper
    {
        return new ContentHelper();
    }

    /**
     * @return ContentHelper
     */
    #[\Override]
    protected function getCasto2t(): ContentHelper
    {
        return $this->o2t;
    }

    #[\Override]
    public static function setUpBeforeClass(bool $withConstCrossCheck = self::WITH_CONST_CROSSCHECK, int $expectedConstsCount = self::EXPECTED_CONSTANT_COUNT): void
    {
        parent::setUpBeforeClass($withConstCrossCheck, $expectedConstsCount);
    }

    public function testConstsExists(): void
    {
        $const = [
            self::CLASS_PREFIX . 'TAG_MACRO_START',
            self::CLASS_PREFIX . 'TAG_MACRO_END',
            self::CLASS_PREFIX . 'TAG_MACRO_VERSION',
            self::CLASS_PREFIX . 'TAG_PARAMETER',
            self::CLASS_PREFIX . 'TAG_BODY_PLAIN',
            self::CLASS_PREFIX . 'TAG_BODY_RICH',
            self::CLASS_PREFIX . 'VAL_BODY_EMPTY',
            self::CLASS_PREFIX . 'VAL_TAG_EMPTY',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    /**
     * @param string           $expected
     * @param string           $macroName
     * @param Map<mixed,mixed> $parameters
     * @param string           $body
     */
    #[DataProvider('providerPrepareMacro')]
    public function testPrepareMacro(string $expected, string $macroName, Map $parameters, string $body): void
    {
        $actual = $this->getCasto2t()::prepareMacro($macroName, $parameters, $body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string           $expected
     * @param Map<mixed,mixed> $parameters
     */
    #[DataProvider('providerPrepareMacroParameter')]
    public function testPrepareMacroParameter(string $expected, Map $parameters): void
    {
        $actual = $this->getCasto2t()::prepareMacroParameter($parameters);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $body
     */
    #[DataProvider('providerPreparePlainBody')]
    public function testPreparePlainBody(string $expected, string $body): void
    {
        $actual = $this->getCasto2t()::preparePlainBody($body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $body
     */
    #[DataProvider('providerPrepareRichBody')]
    public function testPrepareRichTextBody(string $expected, string $body): void
    {
        $actual = $this->getCasto2t()::prepareRichTextBody($body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param HasMacroBodyEnum $expected
     * @param string           $macroName
     */
    #[DataProvider('providerChooseMacroBody')]
    public function testChooseMacroBody(HasMacroBodyEnum $expected, string $macroName): void
    {
        $actual = $this->getCasto2t()::chooseMacroBody($macroName);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $macroName
     * @param string $body
     */
    #[DataProvider('providerPrepareMacroBody')]
    public function testPrepareMacroBody(string $expected, string $macroName, string $body): void
    {
        $actual = $this->getCasto2t()::prepareMacroBody($macroName, $body);

        self::assertEquals($expected, $actual);
    }

    // Dataprovider

    /**
     * @return array<mixed,mixed>
     */
    public static function providerPreparePlainBody(): array
    {
        return [
            'empty' => [YacorapiTestData::DATA_EMPTY, YacorapiTestData::DATA_EMPTY],
            'content' => [self::prepareBodyPlain(YacorapiTestData::MACR_BODY_CONTENT), YacorapiTestData::MACR_BODY_CONTENT],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerPrepareRichBody(): array
    {
        return [
            'empty' => [YacorapiTestData::DATA_EMPTY, YacorapiTestData::DATA_EMPTY],
            'content' => [self::prepareBodyRich(YacorapiTestData::MACR_BODY_CONTENT), YacorapiTestData::MACR_BODY_CONTENT],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerMacroName(): array
    {
        return [
            'empty' => [false, YacorapiTestData::DATA_EMPTY],
            'html' => [false, self::MACRO_HTML],
            'code' => [false, self::MACRO_CODE],
            'section' => [false, self::MACRO_SECTION],
            'column' => [false, self::MACRO_COLUMN],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerChooseMacroBody(): array
    {
        return [
            'empty' => [HasMacroBodyEnum::NONE, YacorapiTestData::DATA_EMPTY],
            'notExist' => [HasMacroBodyEnum::NONE, YacorapiTestData::DATA_NOTEXIST],
            'html' => [HasMacroBodyEnum::PLAIN, self::MACRO_HTML],
            'code' => [HasMacroBodyEnum::PLAIN, self::MACRO_CODE],
            'section' => [HasMacroBodyEnum::RICH, self::MACRO_SECTION],
            'column' => [HasMacroBodyEnum::RICH, self::MACRO_COLUMN],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerPrepareMacroParameter(): array
    {
        return [
            'empty' => ['', new Map()],
            'oneParam' => [
                self::prepareParameter(YacorapiTestData::KEY_NUM1, YacorapiTestData::DATA_NUM1),
//                sprintf(ContentHelper::TAG_PARAM_START . YacorapiTestData::DATA_NUM1 . ContentHelper::TAG_PARAM_END, YacorapiTestData::KEY_NUM1),
                new Map(YacorapiTestData::ARRAY_NUM_KEY1)],
            'twoParam' => [
                self::prepareParameter(0, YacorapiTestData::DATA_BOOL_T) .
//                sprintf(ContentHelper::TAG_PARAM_START . YacorapiTestData::DATA_BOOL_T . ContentHelper::TAG_PARAM_END, 0) .
                self::prepareParameter(1, YacorapiTestData::DATA_BOOL_F),
//                sprintf(ContentHelper::TAG_PARAM_START . YacorapiTestData::DATA_BOOL_F . ContentHelper::TAG_PARAM_END, 1),
                new Map(YacorapiTestData::ARRAY_BOOL2)],
            'threeParam' => [
                self::prepareParameter(YacorapiTestData::KEY_NUM1, YacorapiTestData::DATA_NUM1) .
//                sprintf(ContentHelper::TAG_PARAM_START . YacorapiTestData::DATA_NUM1 . ContentHelper::TAG_PARAM_END, YacorapiTestData::KEY_NUM1) .
                self::prepareParameter(YacorapiTestData::KEY_NUM2, YacorapiTestData::DATA_NUM2) .
//                sprintf(ContentHelper::TAG_PARAM_START . YacorapiTestData::DATA_NUM2 . ContentHelper::TAG_PARAM_END, YacorapiTestData::KEY_NUM2) .
                self::prepareParameter(YacorapiTestData::KEY_NUM3, YacorapiTestData::DATA_NUM3),
//                sprintf(ContentHelper::TAG_PARAM_START . YacorapiTestData::DATA_NUM3 . ContentHelper::TAG_PARAM_END, YacorapiTestData::KEY_NUM3),
                new Map(YacorapiTestData::ARRAY_NUM_KEY3)],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerPrepareMacroBody(): array
    {
        return [
            'emptyAll' => [YacorapiTestData::DATA_EMPTY, YacorapiTestData::DATA_EMPTY, YacorapiTestData::DATA_EMPTY],
            'htmlEmptyBody' => [YacorapiTestData::DATA_EMPTY, self::MACRO_HTML, YacorapiTestData::DATA_EMPTY],
            'codeEmptyBody' => [YacorapiTestData::DATA_EMPTY, self::MACRO_CODE, YacorapiTestData::DATA_EMPTY],
            'sectionEmptyBody' => [YacorapiTestData::DATA_EMPTY, self::MACRO_SECTION, YacorapiTestData::DATA_EMPTY],
            'columnEmptyBody' => [YacorapiTestData::DATA_EMPTY, self::MACRO_COLUMN, YacorapiTestData::DATA_EMPTY],
            'emptyMacroWithBody' => [YacorapiTestData::DATA_EMPTY, YacorapiTestData::DATA_EMPTY, YacorapiTestData::DATA_ALPHA1],
            'htmlWithBody' => [
            self::prepareBodyPlain(YacorapiTestData::DATA_ALPHA1),
                self::MACRO_HTML, YacorapiTestData::DATA_ALPHA1],
            'codeWithBody' => [
            self::prepareBodyPlain(YacorapiTestData::DATA_ALPHA1),
                self::MACRO_CODE, YacorapiTestData::DATA_ALPHA1],
            'sectionWithBody' => [
                self::prepareBodyRich(YacorapiTestData::DATA_ALPHA1),
                self::MACRO_SECTION, YacorapiTestData::DATA_ALPHA1],
            'columnWithBody' => [
            self::prepareBodyRich(YacorapiTestData::DATA_ALPHA1),
            self::MACRO_COLUMN, YacorapiTestData::DATA_ALPHA1],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerPrepareMacro(): array
    {
        return [
            'emptyParam' => [
                self::prepareMacro(YacorapiTestData::DATA_EMPTY), YacorapiTestData::DATA_EMPTY, new Map(), YacorapiTestData::DATA_EMPTY],
            'oneParam' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                        self::prepareParameter(0, YacorapiTestData::DATA_ALPHA1) .
                        ContentHelper::TAG_MACRO_END,
                    YacorapiTestData::DATA_ALPHA2,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                YacorapiTestData::DATA_ALPHA2, new Map(YacorapiTestData::ARRAY_ALPHA1), YacorapiTestData::DATA_ALPHA3],
            'twoParam' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                        self::prepareParameter(0, YacorapiTestData::DATA_BOOL_T) .
                        self::prepareParameter(1, YacorapiTestData::DATA_BOOL_F) .
                        ContentHelper::TAG_MACRO_END,
                    YacorapiTestData::DATA_ALPHA1,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                YacorapiTestData::DATA_ALPHA1, new Map(YacorapiTestData::ARRAY_BOOL2), YacorapiTestData::DATA_ALPHA2],
            'threeParam' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                        self::prepareParameter(0, YacorapiTestData::DATA_NUM1) .
                        self::prepareParameter(1, YacorapiTestData::DATA_NUM2) .
                        self::prepareParameter(2, YacorapiTestData::DATA_NUM3) .
                        ContentHelper::TAG_MACRO_END,
                    YacorapiTestData::DATA_ALPHA5,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                YacorapiTestData::DATA_ALPHA5, new Map(YacorapiTestData::ARRAY_NUM3), YacorapiTestData::DATA_ALPHA2],
            'codePlainBody' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                        self::prepareBodyPlain(YacorapiTestData::MACR_BODY_CONTENT) .
                        ContentHelper::TAG_MACRO_END,
                    self::MACRO_CODE,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                self::MACRO_CODE, new Map(), YacorapiTestData::MACR_BODY_CONTENT],
            'sectionRichBody' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                        self::prepareBodyRich(YacorapiTestData::MACR_BODY_CONTENT) .
                        ContentHelper::TAG_MACRO_END,
                    self::MACRO_SECTION,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                self::MACRO_SECTION, new Map(), YacorapiTestData::MACR_BODY_CONTENT],
        ];
    }

    // Helper

    protected static function prepareMacro(mixed $macroName): string
    {
        return sprintf(ContentHelper::TAG_MACRO_START . ContentHelper::TAG_MACRO_END, $macroName, ContentHelper::TAG_MACRO_VERSION);
    }

    protected static function prepareParameter(mixed $paramName, mixed $paramValue): string
    {
        return sprintf(ContentHelper::TAG_PARAMETER, $paramName, $paramValue);
    }

    protected static function prepareBodyPlain(mixed $bodyContent): string
    {
        return sprintf(ContentHelper::TAG_BODY_PLAIN, $bodyContent);
    }

    protected static function prepareBodyRich(mixed $bodyContent): string
    {
        return sprintf(ContentHelper::TAG_BODY_RICH, $bodyContent);
    }
}
