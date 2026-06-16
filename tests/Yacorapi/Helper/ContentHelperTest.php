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
use ollily\Tools\Test\TestData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\ConstantCheckTestCase;

class ContentHelperTest extends ConstantCheckTestCase
{
    public const string CLASS_PREFIX = ContentHelperTest::class . self::C_STATIC_SEP;

    public const string MACRO_HTML = 'html';

    public const string MACRO_SECTION = 'section';

    public const string MACRO_COLUMN = 'column';

    protected const int EXPECTED_CONSTANT_COUNT = 13;

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
            self::CLASS_PREFIX . 'MACROBODY_PLAIN',
            self::CLASS_PREFIX . 'MACROBODY_RICHTEXT',
            self::CLASS_PREFIX . 'CHOOSE_BODY_RICHTEXT',
            self::CLASS_PREFIX . 'CHOOSE_BODY_PLAIN',
            self::CLASS_PREFIX . 'TAG_PARAM_START',
            self::CLASS_PREFIX . 'TAG_PARAM_END',
            self::CLASS_PREFIX . 'TAG_PLAIN_START',
            self::CLASS_PREFIX . 'TAG_PLAIN_END',
            self::CLASS_PREFIX . 'TAG_RICH_START',
            self::CLASS_PREFIX . 'TAG_RICH_END',
            self::CLASS_PREFIX . 'TAG_MACRO_START',
            self::CLASS_PREFIX . 'TAG_MACRO_END',
            self::CLASS_PREFIX . 'TAG_MACRO_VERSION',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }

    /**
     * @param string                $expected
     * @param null|Map<mixed,mixed> $parameters
     */
    #[DataProvider('providerParameters')]
    public function testPrepareMacroParameter(string $expected, ?Map $parameters): void
    {
        $actual = $this->getCasto2t()::prepareMacroParameter($parameters);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $body
     */
    #[DataProvider('providerPlainBody')]
    public function testPreparePlainBody(string $expected, string $body): void
    {
        $actual = $this->getCasto2t()::preparePlainBody($body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $body
     */
    #[DataProvider('providerRichBody')]
    public function testPrepareRichTextBody(string $expected, string $body): void
    {
        $actual = $this->getCasto2t()::prepareRichTextBody($body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $macroName
     * @param string $body
     */
    #[DataProvider('providerMacroNameBody')]
    public function testPrepareMacroBody(string $expected, string $macroName, string $body): void
    {
        $actual = $this->getCasto2t()::prepareMacroBody($macroName, $body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $macroName
     */
    #[DataProvider('providerChooseMacroBody')]
    public function testChooseMacroBody(string $expected, string $macroName): void
    {
        $actual = $this->getCasto2t()::chooseMacroBody($macroName);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string                $expected
     * @param string                $macroName
     * @param null|Map<mixed,mixed> $parameters
     * @param string                $body
     */
    #[DataProvider('providerMacroNameParametersBody')]
    public function testPrepareMacro(string $expected, string $macroName, ?Map $parameters, string $body): void
    {
        $actual = $this->getCasto2t()::prepareMacro($macroName, $parameters, $body);

        self::assertEquals($expected, $actual);
    }

    // Dataprovider

    /**
     * @return array<mixed,mixed>
     */
    public static function providerPlainBody(): array
    {
        return [
            'empty' => [TestData::DATA_EMPTY, TestData::DATA_EMPTY],
            'html' => [ContentHelper::TAG_PLAIN_START . self::MACRO_HTML . ContentHelper::TAG_PLAIN_END, self::MACRO_HTML],
            'section' => [ContentHelper::TAG_PLAIN_START . self::MACRO_SECTION . ContentHelper::TAG_PLAIN_END, self::MACRO_SECTION],
            'column' => [ContentHelper::TAG_PLAIN_START . self::MACRO_COLUMN . ContentHelper::TAG_PLAIN_END, self::MACRO_COLUMN],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerRichBody(): array
    {
        return [
            'empty' => [TestData::DATA_EMPTY, TestData::DATA_EMPTY],
            'html' => [ContentHelper::TAG_RICH_START . self::MACRO_HTML . ContentHelper::TAG_RICH_END, self::MACRO_HTML],
            'section' => [ContentHelper::TAG_RICH_START . self::MACRO_SECTION . ContentHelper::TAG_RICH_END, self::MACRO_SECTION],
            'column' => [ContentHelper::TAG_RICH_START . self::MACRO_COLUMN . ContentHelper::TAG_RICH_END, self::MACRO_COLUMN],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerMacroName(): array
    {
        return [
            'empty' => [false, TestData::DATA_EMPTY],
            'html' => [false, self::MACRO_HTML],
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
            'empty' => [ContentHelper::CHOOSE_BODY_PLAIN, TestData::DATA_EMPTY],
            'html' => [ContentHelper::CHOOSE_BODY_PLAIN, self::MACRO_HTML],
            'section' => [ContentHelper::CHOOSE_BODY_RICHTEXT, self::MACRO_SECTION],
            'column' => [ContentHelper::CHOOSE_BODY_RICHTEXT, self::MACRO_COLUMN],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerParameters(): array
    {
        return [
            'null' => ['', null],
            'empty' => ['', new Map()],
            'oneParam' => [
                sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_ALPHA1 . ContentHelper::TAG_PARAM_END, 0),
                new Map(TestData::ARRAY_ALPHA1)],
            'twoParam' => [
                sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_BOOL_T . ContentHelper::TAG_PARAM_END, 0) .
                sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_BOOL_F . ContentHelper::TAG_PARAM_END, 1), new Map(TestData::ARRAY_BOOL2)],
            'threeParam' => [
                sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_NUM1 . ContentHelper::TAG_PARAM_END, 0) .
                sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_NUM2 . ContentHelper::TAG_PARAM_END, 1) .
                sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_NUM3 . ContentHelper::TAG_PARAM_END, 2), new Map(TestData::ARRAY_NUM3)],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerMacroNameBody(): array
    {
        return [
            'emptyAll' => [TestData::DATA_EMPTY, TestData::DATA_EMPTY, TestData::DATA_EMPTY],
            'htmlEmptyBody' => [TestData::DATA_EMPTY, self::MACRO_HTML, TestData::DATA_EMPTY],
            'sectionEmptyBody' => [TestData::DATA_EMPTY, self::MACRO_SECTION, TestData::DATA_EMPTY],
            'columnEmptyBody' => [TestData::DATA_EMPTY, self::MACRO_COLUMN, TestData::DATA_EMPTY],
            'emptyMacroWithBody' => [
                ContentHelper::TAG_PLAIN_START . TestData::DATA_ALPHA1 . ContentHelper::TAG_PLAIN_END,
                TestData::DATA_EMPTY, TestData::DATA_ALPHA1],
            'htmlWithBody' => [
                ContentHelper::TAG_PLAIN_START . TestData::DATA_ALPHA1 . ContentHelper::TAG_PLAIN_END,
                self::MACRO_HTML, TestData::DATA_ALPHA1],
            'sectionWithBody' => [
                ContentHelper::TAG_RICH_START . TestData::DATA_ALPHA1 . ContentHelper::TAG_RICH_END,
                self::MACRO_SECTION, TestData::DATA_ALPHA1],
            'columnWithBody' => [
                ContentHelper::TAG_RICH_START . TestData::DATA_ALPHA1 . ContentHelper::TAG_RICH_END,
                self::MACRO_COLUMN, TestData::DATA_ALPHA1],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerMacroNameParametersBody(): array
    {
        return [
            'nullParam' => [
                sprintf(ContentHelper::TAG_MACRO_START . ContentHelper::TAG_MACRO_END, TestData::DATA_EMPTY, ContentHelper::TAG_MACRO_VERSION),
                TestData::DATA_EMPTY, null, TestData::DATA_EMPTY],
            'emptyParam' => [
                sprintf(ContentHelper::TAG_MACRO_START . ContentHelper::TAG_MACRO_END, TestData::DATA_EMPTY, ContentHelper::TAG_MACRO_VERSION),
                TestData::DATA_EMPTY, new Map(), TestData::DATA_EMPTY],
            'oneParam' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                    sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_ALPHA1 . ContentHelper::TAG_PARAM_END, 0) .
                    TestData::DATA_ALPHA3 .
                    ContentHelper::TAG_MACRO_END,
                    TestData::DATA_ALPHA2,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                TestData::DATA_ALPHA2, new Map(TestData::ARRAY_ALPHA1), TestData::DATA_ALPHA3],
            'twoParam' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                    sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_BOOL_T . ContentHelper::TAG_PARAM_END, 0) .
                    sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_BOOL_F . ContentHelper::TAG_PARAM_END, 1) .
                    TestData::DATA_ALPHA2 .
                    ContentHelper::TAG_MACRO_END,
                    TestData::DATA_ALPHA1,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                TestData::DATA_ALPHA1, new Map(TestData::ARRAY_BOOL2), TestData::DATA_ALPHA2],
            'threeParam' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                    sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_NUM1 . ContentHelper::TAG_PARAM_END, 0) .
                    sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_NUM2 . ContentHelper::TAG_PARAM_END, 1) .
                    sprintf(ContentHelper::TAG_PARAM_START . TestData::DATA_NUM3 . ContentHelper::TAG_PARAM_END, 2) .
                    TestData::DATA_ALPHA2 .
                    ContentHelper::TAG_MACRO_END,
                    TestData::DATA_ALPHA5,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                TestData::DATA_ALPHA5, new Map(TestData::ARRAY_NUM3), TestData::DATA_ALPHA2],
        ];
    }
}
