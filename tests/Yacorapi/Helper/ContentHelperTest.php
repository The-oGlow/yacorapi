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
use oglow\tools\Yacorapi\YacorapiTestData as YTD;
use ollily\Tools\Reflection\UnavailableMethodsTrait;
use PHPUnit\Framework\EasyGoingTestCase;

class ContentHelperTest extends EasyGoingTestCase
{
    Use UnavailableMethodsTrait;
    
    #[\Override]
    protected static function prepareO2t(): object
    {
        return ContentHelper::i();
    }

    #[\Override]
    protected function getCasto2t(): ContentHelper
    {
        return $this->o2t::i();
    }

    /**
     * @param string           $expected
     * @param string           $macroName
     * @param Map<mixed,mixed> $parameters
     * @param string           $body
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerPrepareMacro')]
    public function ztestPrepareMacro(string $expected, string $macroName, Map $parameters, string $body): void
    {
        $actual = $this->getCasto2t()->prepareMacro($macroName, $parameters, $body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string           $expected
     * @param Map<mixed,mixed> $parameters
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerPrepareMacroParameter')]
    public function ztestPrepareMacroParameter(string $expected, Map $parameters): void
    {
        $actual = $this->getCasto2t()->prepareMacroParameter($parameters);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $body
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerPreparePlainBody')]
    public function ztestPreparePlainBody(string $expected, string $body): void
    {
        $actual = $this->getCasto2t()->preparePlainBody($body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $body
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerPrepareRichBody')]
    public function ztestPrepareRichTextBody(string $expected, string $body): void
    {
        $actual = $this->getCasto2t()->prepareRichTextBody($body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param HasMacroBodyEnum $expected
     * @param string           $macroName
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerChooseMacroBody')]
    public function ztestChooseMacroBody(HasMacroBodyEnum $expected, string $macroName): void
    {
        $actual = $this->callMethodOnO2t('chooseMacroBody', [$macroName]);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $macroName
     * @param string $body
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerPrepareMacroBody')]
    public function ztestPrepareMacroBody(string $expected, string $macroName, string $body): void
    {
        $actual = $this->getCasto2t()->prepareMacroBody($macroName, $body);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string $expected
     * @param string $searchMacro
     * @param string $replaceMacro
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerReplaceMacro')]
    public function testReplaceMacro(bool $expected, string $expectedMacro, string $searchMacro, string $replaceMacro): void
    {
        $macroOriginal= $this->getCasto2t()->prepareMacro($searchMacro, new Map([YTD::KEY_ALPHA1 => YTD::DATA_ALPHA1]));
        $content = sprintf(YTD::TAG_ROOT, '', $macroOriginal);
        $domDoc = YTD::prepareDOMDocument($content);
        if ($expected) {
            self::assertStringContainsString($searchMacro, $content);
            self::assertStringNotContainsString($replaceMacro, $content);
        }
        $actual = $this->getCasto2t()->replaceMacro($searchMacro, $replaceMacro, $domDoc);
        $actualXml= $actual->saveXML();
        
        self::assertStringContainsString($expectedMacro, $actualXml);
    }

    // Dataprovider

    /**
     * @return array<mixed>
     */
    public static function providerReplaceMacro(): array
    {
        $expected = ContentHelper::TAG_PARAMETER_NAME_NAME.'="%s"';
        return [
            'emptyEmpty' => [false, sprintf($expected,YTD::MACRO_EMPTY), YTD::MACRO_EMPTY, YTD::MACRO_EMPTY],
            'searchSearch' => [false, sprintf($expected, YTD::MACRO_SEARCH), YTD::MACRO_SEARCH, YTD::MACRO_SEARCH],
            'replaceReplace' => [false, sprintf($expected, YTD::MACRO_REPLACE), YTD::MACRO_REPLACE, YTD::MACRO_REPLACE],
            'searchEmpty' => [false, sprintf($expected, YTD::MACRO_EMPTY), YTD::MACRO_SEARCH, YTD::MACRO_EMPTY],
            'emptyReplace' => [false, sprintf($expected, YTD::MACRO_EMPTY), YTD::MACRO_EMPTY, YTD::MACRO_EMPTY],
            'searchReplace' => [true, sprintf($expected, YTD::MACRO_REPLACE), YTD::MACRO_SEARCH, YTD::MACRO_REPLACE],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function providerPreparePlainBody(): array
    {
        return [
            'empty' => [YTD::DATA_EMPTY, YTD::DATA_EMPTY],
            'content' => [self::prepareBodyPlain(YTD::MACR_BODY_CONTENT), YTD::MACR_BODY_CONTENT],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function providerPrepareRichBody(): array
    {
        return [
            'empty' => [YTD::DATA_EMPTY, YTD::DATA_EMPTY],
            'content' => [self::prepareBodyRich(YTD::MACR_BODY_CONTENT), YTD::MACR_BODY_CONTENT],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function providerMacroName(): array
    {
        return [
            'empty' => [false, YTD::DATA_EMPTY],
            'html' => [false, YTD::MACRO_IRL_HTML],
            'code' => [false, YTD::MACRO_IRL_CODE],
            'section' => [false, YTD::MACRO_IRL_SECTION],
            'column' => [false, YTD::MACRO_IRL_COLUMN],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function providerChooseMacroBody(): array
    {
        return [
            'empty' => [HasMacroBodyEnum::NONE, YTD::DATA_EMPTY],
            'notExist' => [HasMacroBodyEnum::NONE, YTD::DATA_NOTEXIST],
            'html' => [HasMacroBodyEnum::PLAIN, YTD::MACRO_IRL_HTML],
            'code' => [HasMacroBodyEnum::PLAIN, YTD::MACRO_IRL_CODE],
            'section' => [HasMacroBodyEnum::RICH, YTD::MACRO_IRL_SECTION],
            'column' => [HasMacroBodyEnum::RICH, YTD::MACRO_IRL_COLUMN],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function providerPrepareMacroParameter(): array
    {
        return [
            'empty' => ['', new Map()],
            'oneParam' => [
                self::prepareParameter(YTD::KEY_NUM1, YTD::DATA_NUM1),
                new Map(YTD::ARRAY_NUM_KEY1)],
            'twoParam' => [
                self::prepareParameter(0, YTD::DATA_BOOL_T) .
                self::prepareParameter(1, YTD::DATA_BOOL_F),
                new Map(YTD::ARRAY_BOOL2)],
            'threeParam' => [
                self::prepareParameter(YTD::KEY_NUM1, YTD::DATA_NUM1) .
                self::prepareParameter(YTD::KEY_NUM2, YTD::DATA_NUM2) .
                self::prepareParameter(YTD::KEY_NUM3, YTD::DATA_NUM3),
                new Map(YTD::ARRAY_NUM_KEY3)],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function providerPrepareMacroBody(): array
    {
        return [
            'emptyAll' => [YTD::DATA_EMPTY, YTD::DATA_EMPTY, YTD::DATA_EMPTY],
            'htmlEmptyBody' => [YTD::DATA_EMPTY, YTD::MACRO_IRL_HTML, YTD::DATA_EMPTY],
            'codeEmptyBody' => [YTD::DATA_EMPTY, YTD::MACRO_IRL_CODE, YTD::DATA_EMPTY],
            'sectionEmptyBody' => [YTD::DATA_EMPTY, YTD::MACRO_IRL_SECTION, YTD::DATA_EMPTY],
            'columnEmptyBody' => [YTD::DATA_EMPTY, YTD::MACRO_IRL_COLUMN, YTD::DATA_EMPTY],
            'emptyMacroWithBody' => [YTD::DATA_EMPTY, YTD::DATA_EMPTY, YTD::DATA_ALPHA1],
            'htmlWithBody' => [
                self::prepareBodyPlain(YTD::DATA_ALPHA1),
                YTD::MACRO_IRL_HTML, YTD::DATA_ALPHA1],
            'codeWithBody' => [
                self::prepareBodyPlain(YTD::DATA_ALPHA1),
                YTD::MACRO_IRL_CODE, YTD::DATA_ALPHA1],
            'sectionWithBody' => [
                self::prepareBodyRich(YTD::DATA_ALPHA1),
                YTD::MACRO_IRL_SECTION, YTD::DATA_ALPHA1],
            'columnWithBody' => [
                self::prepareBodyRich(YTD::DATA_ALPHA1),
                YTD::MACRO_IRL_COLUMN, YTD::DATA_ALPHA1],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function providerPrepareMacro(): array
    {
        return [
            'emptyParam' => [
                self::prepareMacro(YTD::DATA_EMPTY), YTD::DATA_EMPTY, new Map(), YTD::DATA_EMPTY],
            'oneParam' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                    self::prepareParameter(0, YTD::DATA_ALPHA1) .
                    ContentHelper::TAG_MACRO_END,
                    YTD::DATA_ALPHA2,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                YTD::DATA_ALPHA2, new Map(YTD::ARRAY_ALPHA1), YTD::DATA_ALPHA3],
            'twoParam' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                    self::prepareParameter(0, YTD::DATA_BOOL_T) .
                    self::prepareParameter(1, YTD::DATA_BOOL_F) .
                    ContentHelper::TAG_MACRO_END,
                    YTD::DATA_ALPHA1,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                YTD::DATA_ALPHA1, new Map(YTD::ARRAY_BOOL2), YTD::DATA_ALPHA2],
            'threeParam' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                    self::prepareParameter(0, YTD::DATA_NUM1) .
                    self::prepareParameter(1, YTD::DATA_NUM2) .
                    self::prepareParameter(2, YTD::DATA_NUM3) .
                    ContentHelper::TAG_MACRO_END,
                    YTD::DATA_ALPHA5,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                YTD::DATA_ALPHA5, new Map(YTD::ARRAY_NUM3), YTD::DATA_ALPHA2],
            'codePlainBody' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                    self::prepareBodyPlain(YTD::MACR_BODY_CONTENT) .
                    ContentHelper::TAG_MACRO_END,
                    YTD::MACRO_IRL_CODE,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                YTD::MACRO_IRL_CODE, new Map(), YTD::MACR_BODY_CONTENT],
            'sectionRichBody' => [
                sprintf(
                    ContentHelper::TAG_MACRO_START .
                    self::prepareBodyRich(YTD::MACR_BODY_CONTENT) .
                    ContentHelper::TAG_MACRO_END,
                    YTD::MACRO_IRL_SECTION,
                    ContentHelper::TAG_MACRO_VERSION
                ),
                YTD::MACRO_IRL_SECTION, new Map(), YTD::MACR_BODY_CONTENT],
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
