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

use Ds\Map;
use oglow\tools\Addon\Atlassian\Extension\AdminExtension;
use oglow\tools\Addon\Atlassian\Extension\AtlassianExtension;
use oglow\tools\Addon\Projectdoc\Extension\ProjectdocExtension;
use oglow\tools\Addon\ThirdParty\Extension\ThirdPartyExtension;
use oglow\tools\Addon\UserMacro\Extension\UserMacroExtension;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\Extension\IExtension;
use oglow\tools\Yacorapi\Extension\RapiClientExtension;
use ollily\Tools\Test\TestData;

/**
 * @SuppressWarnings("PHPMD.CamelCaseMethodName")
 * @SuppressWarnings("PHPMD.CamelCasePropertyName")
 */
// @phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
class YacorapiTestData extends TestData
{
    /* Atlassian Extension '/

      /** All extensions */

    public const EXTENSIONS_COUNT_TOTAL = 6;

    /** Definition of all extensions */
    public const EXTENSIONS_NAMES = [
        IExtension::EXTENSION_RAPI_CLIENT => RapiClientExtension::class,
        IExtension::EXTENSION_ATLASSIAN => AtlassianExtension::class,
        IExtension::EXTENSION_ATLASSIAN_ADMIN => AdminExtension::class,
        IExtension::EXTENSION_ATLASSIAN_USER_MACRO => UserMacroExtension::class,
        IExtension::EXTENSION_THIRD_PARTY => ThirdPartyExtension::class,
        IExtension::EXTENSION_PROJECTDOC_TOOLBOX => ProjectdocExtension::class,
    ];

    /** Array of extensions which will be verified */
    public const EXTENSIONS_VERIFY = [RapiClientExtension::class, AtlassianExtension::class];

    /** Single extension which will be verified */
    public const EXTENSION_VERIFY = [RapiClientExtension::class];

    public const EXT_USER_MACRO_MACRO = 3;

    public const EXT_USER_MACRO_ADDON = 1;

    public const EXT_ATLASSIAN_ADDON = 2;

    public const EXT_PDT_ADDON = 4;

    public const EXT_3PARTY_MACRO = 69;

    public const EXT_ATLASSIAN_MACRO = 60;

    public const EXT_PDT_MACRO = 87;

    public const EXT_3PARTY_ADDON = 11;

    // Atlassian Addon

    /** All addons from all extensions */
    public const ADDONS_COUNT_TOTAL = 18;

    /** Names of one addon from each extension */
    public const ADDONS_NAMES = [
        'Atlassian Confluence Macros Addon',
        'Confluence HTML Macros',
        'Confluence User Macros',
        'Advanced Roadmaps for Jira in Confluence',
        'Advanced Tables for Confluence',
        'BPMN Modeler Enterprise',
        'Comala Document Management',
        'Draw.io Confluence Plugin',
        'Linking for Confluence',
        'Scroll Documents for Confluence',
        'Scroll Exporter Extensions',
        'Scroll Versions for Confluence',
        'Scroll Runtime for Confluence',
        'Table Filter, Charts and Spreadsheets for Confluence',
        'projectdoc Core Doctypes',
        'projectdoc for Agile Planning',
        'projectdoc for Software Development',
        'projectdoc Toolbox for Confluence',
    ];

    public const CLAZZ_ALL_ADDON = '\oglow\tools\Yacorapi\Macro\AllAddon';

    public const CLAZZ_SINGLE_ADDON = '\oglow\tools\Yacorapi\Macro\SingleAddon';

    public const CLAZZ_PDT = '\oglow\tools\Addon\Projectdoc\Macro\ProjectdocAddon';

    public const CLAZZ_3PARTY = '\oglow\tools\Addon\ThirdParty\Macro\ThirdPartyAddon';

    public const CLAZZ_BLOCKER_ADDON = '\oglow\tools\Yacorapi\Macro\BlockerAddon';

    public const CLAZZ_ATLASSIAN_ADDON = '\oglow\tools\Addon\Atlassian\Macro\AtlassianAddon';

    public const CLAZZ_USER_MACRO_ADDON = '\oglow\tools\Addon\UserMacro\Macro\UserMacroAddon';

    /** The name of an addon which does not exists in the system */
    public const ADDONS_NAME_NOTEXIST = 'NOTEXIST-ADDON';

    /** Array of specific addons which will be checked */
    public const ADDONS_VERIFY = ['Atlassian Confluence Macros Addon', 'Confluence User Macros'];

    /** Single addon which will be verified */
    public const ADDON_VERIFY = 'Atlassian Confluence Macros Addon';

    // (Addon-) Mode
    public const MODE_NOTEXIST = 'NOTEXIST-MODE';

    public const MODE_SINGLE_ADDON_COUNT_TOTAL = 1;

    public const MODE_SINGLE_MACRO_COUNT_TOTAL = 5;

    public const MODE_SINGLE_ADDON_NAME = 'single-addon';

    public const MODE_SINGLE_ADDON_NAME_MACRO_COUNT = 5;

    public const MODE_SINGLE_ADDON_NAME_NOTEXIST = self::ADDONS_NAME_NOTEXIST;

    public const MODE_SINGLE_ADDON_NAME_NOTEXIST_MACRO_COUNT = 0;

    public const MODE_BLOCKER_ADDON_COUNT_TOTAL = 11;

    public const MODE_BLOCKER_MACRO_COUNT_TOTAL = 87;

    public const MODE_BLOCKER_ADDON_NAME = 'Scroll Runtime for Confluence';

    public const MODE_BLOCKER_ADDON_NAME_MACRO_COUNT = 7;

    public const MODE_BLOCKER_ADDON_NAME_NOTEXISTS = self::ADDONS_NAME_NOTEXIST;

    public const MODE_BLOCKER_ADDON_NAME_NOTEXISTS_MACRO_COUNT = 0;

    public const MODE_ALL_ADDON_COUNT_TOTAL = 18;

    public const MODE_ALL_MACRO_COUNT_TOTAL = 219;

    public const MODE_ALL_ADDON_NAME = 'Scroll Documents for Confluence';

    public const MODE_ALL_ADDON_NAME_MACRO_COUNT = 1;

    public const MODE_ALL_ADDON_NAME_NOTEXIST = self::ADDONS_NAME_NOTEXIST;

    public const MODE_ALL_ADDON_NAME_NOTEXIST_MACRO_COUNT = 0;

    // Atlassian Macro

    /** All macros from all addons */
    public const MACROS_COUNT_TOTAL = 219;

    /** Array of specific macros which will be checked */
    public const MACROS_VERIFY = ['children', 'code', 'create-from-template', 'section', 'toc'];

    /** Single macro which will be checked */
    public const MACRO_VERIFY = 'create-from-template';

    // Unspecific test data
    public const ADDON_1 = 'MyAddon';

    public const ADDON_1_A = 'macro1';

    public const ADDON_1_B = 'macro3';

    public const ADDON_1_C = 'macro5';

    public const ADDON_1_ORDER = [YacorapiTestData::ADDON_1_A, YacorapiTestData::ADDON_1_B, YacorapiTestData::ADDON_1_C];

    public const ADDON_2 = 'OtherAddon';

    public const ADDON_2_A = 'macro2';

    public const ADDON_2_C = 'macro6';

    public const ADDON_2_B = 'macro4';

    public const ADDON_2_ORDER = [YacorapiTestData::ADDON_2_A, YacorapiTestData::ADDON_2_B, YacorapiTestData::ADDON_2_C];

    public const C_RESPONSE_SIZE_EMPTY = 0;

    public const C_PAGEID_NOTEXIST = 0;

    public const C_PAGEID_EXIST = 2;

    public const C_PAGEID_NEW = 11;

    public const C_SPACE_EMPTY = '';

    public const C_SPACE_EXIST_KEY = 'SPCEX';

    public const C_SPACE_EXIST_ID = 12345;

    public const C_SPACE_EXIST_NAME = 'Existing Space';

    public const C_SPACE_EXIST_DESCRIPTION = 'A space which exists.';

    public const C_SPACE_EXIST_STATUS = RequestParameterData::STATUS_TYPE_CURRENT;

    public const C_SPACE_EXIST_TYPE = RequestParameterData::SPACE_TYPE_GLOBAL;

    public const C_FILTERTERM_01 = 'filter=1';

    public const C_PREPURL_01 = 'title~Test';

    public const C_SEARCHTERM_EMPTY = '';

    public const C_SEARCHTERM_01 = 'searchtext';

    public const C_SEARCHPAGEID_01 = 532951146;

    public const C_SEARCHPAGETITLE_01 = 'SEARCHPAGETITLE_01';

    public const C_SEARCHPAGESPACE_01 = self::C_SPACE_EXIST_KEY;

    public const C_SEARCHPAGESPARENT_01 = self::C_PAGEID_EXIST;

    public const HTML_PAGE = '<!DOCTYPE html><html><head><title>#pagetitle#</title></head><body></body></html>';

    // Macro Code
    public const MACR_PH = '#ph#';

    public const MACR_MACRO_ID = '12345678-1234-1234-1234-123456789012';

    public const MACR_MACRO_TAG_START = '<ac:structured-macro ac:macro-id="' .
        self::MACR_MACRO_ID .
        '" ac:name="' .
        self::MACR_PH .
        '" ac:schema-version="1">';

    public const MACR_MACRO_TAG_END = '</ac:structured-macro>';

    public const MACR_BODY_EMPTY = '';

    public const MACR_BODY_SIMPLE = '<body></body>';

    public const MACR_BODY_INVALID = 'invalidBody';

    // Macro Doctype
    public const MACR_DOCTYPE_NEW = 'newDT';

    public const MACR_DOCTYPE_OLD = 'oldDT';

    public const MACR_DOCTYPE_WRONG = self::NOTEXIST_NAME;

    public const MACR_DOCTYPE_EMPTY = '';
    // Macro Code Name

    public const MACR_PROJECTDOC_PROPERTIES_MARKER = 'projectdoc-properties-marker';

    public const MACR_PDT_PROPERTIES_MARKER_01 = '<ac:parameter ac:name="doctype">';

    public const MACR_PDT_PROPERTIES_MARKER_02 = '</ac:parameter>';

    // Response

    /** @var array<mixed,mixed> */
    private static array $RESP_HEAD_SEARCHPAGEID_01;

    /** @var array<mixed,mixed> */
    private static array $RESP_BODY;

    /** @var array<mixed,mixed> */
    private static array $RESP_CONTENTFILTER_RESULT;

    /** @var array<mixed,mixed> */
    private static array $RESP_SEARCH_RESULT;

    /** @var array<mixed,mixed> */
    private static array $RESP_SCAN_RESULT;

    public const RESP_RESTRICTION = [
        IResponse::KEY_RESTRICTIONS => [
            IResponse::KEY_READ => [
                IResponse::KEY_OPERATION => IResponse::KEY_READ,
                IResponse::KEY_RESTRICTIONS => [
                    IResponse::KEY_USER => [],
                    IResponse::KEY_GROUP => [],
                ],
            ],
            IResponse::KEY_UPDATE => [
                IResponse::KEY_OPERATION => IResponse::KEY_UPDATE,
                IResponse::KEY_RESTRICTIONS => [
                    IResponse::KEY_USER => [],
                    IResponse::KEY_GROUP => [],
                ],
            ],
        ],
    ];

    // Misc Constants

    private function __construct()
    {
        // Hide the public constructor
    }

    // Static functions

    /**
     * @return array<mixed,mixed>
     */
    public static function RESP_HEAD_SEARCHPAGEID_01(): array
    {
        self::$RESP_HEAD_SEARCHPAGEID_01 = [
            IResponse::KEY_ID => self::C_SEARCHPAGEID_01,
            IResponse::KEY_TITLE => self::C_SEARCHPAGETITLE_01,
        ];
        self::$RESP_HEAD_SEARCHPAGEID_01 = array_merge(self::$RESP_HEAD_SEARCHPAGEID_01, self::prepareResponseSpace(self::C_SEARCHPAGESPACE_01));
        self::$RESP_HEAD_SEARCHPAGEID_01 = array_merge(self::$RESP_HEAD_SEARCHPAGEID_01, self::prepareResponseAncestor(self::C_SEARCHPAGESPARENT_01));

        return self::$RESP_HEAD_SEARCHPAGEID_01;
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function RESP_BODY(): array
    {
        self::$RESP_BODY =             self::prepareResponseBody(YacorapiTestData::HTML_PAGE)        ;

        return self::$RESP_BODY;
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function RESP_CONTENTFILTER_RESULT(): array
    {
        self::$RESP_CONTENTFILTER_RESULT = self::prepareResponseResults([self::RESP_HEAD_SEARCHPAGEID_01()]);

        return self::$RESP_CONTENTFILTER_RESULT;
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function RESP_SCAN_RESULT(): array
    {
        self::$RESP_SCAN_RESULT = self::prepareResponseResults([self::RESP_HEAD_SEARCHPAGEID_01()]);

        return self::$RESP_SCAN_RESULT;
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function RESP_SEARCH_RESULT(): array
    {
        self::$RESP_SEARCH_RESULT = self::prepareResponseResults([self::RESP_HEAD_SEARCHPAGEID_01()]);

        return self::$RESP_SEARCH_RESULT;
    }

    public static function getMacroCode(string $macroName): string
    {
        $macroCode = static::setMacroName($macroName);
        $cleanMacroName = str_replace('-', '', $macroName);
        $macroCode .= static::$cleanMacroName(); // @phpstan-ignore staticMethod.dynamicName
        $macroCode .= static::MACR_MACRO_TAG_END;

        return $macroCode;
    }

    public static function setMacroName(string $macroName): string
    {
        return str_replace(self::MACR_PH, $macroName, self::MACR_MACRO_TAG_START);
    }

    public static function repPH(string $text, string $replace): string
    {
        return str_replace(self::MACR_PH, $replace, $text);
    }

    /**
     * @param array<mixed,mixed> $items
     *
     * @return array<mixed,mixed>
     */
    public static function prepareResponseResults(array $items): array
    {
        return [
            IResponse::KEY_TOTAL_SIZE => count($items),
            IResponse::KEY_RESULTS => $items,
        ];
    }

    /**
     * @param string                $text
     * @param null|Map<mixed,mixed> $parameters
     *
     * @return array<mixed,mixed>
     */
    public static function prepareResponseSpace(string $text = '', ?Map $parameters = null): array
    {
        if (!is_null($parameters)) {
            $text = $parameters->get(RequestParameterData::PROP_SPACE)[RequestParameterData::PROP_KEY];
        }

        return [
            IResponse::KEY_SPACE => [
                IResponse::KEY_KEY => $text,
            ]];
    }

    /**
     * @param mixed                 $text
     * @param null|Map<mixed,mixed> $parameters
     *
     * @return array<mixed,mixed>
     */
    public static function prepareResponseAncestor(mixed $text = '', ?Map $parameters = null): array
    {
        if (!is_null($parameters)) {
            $text = $parameters->get(RequestParameterData::PROP_ANCESTORS)[RequestParameterData::PROP_KEY];
        }

        return [
            IResponse::KEY_ANCESTORS => [
                IResponse::KEY_ID => $text,
            ]];
    }

    /**
     * @param string                $text
     * @param null|Map<mixed,mixed> $parameters
     *
     * @return array<mixed,mixed>
     */
    public static function prepareResponseBody(string $text = '', ?Map $parameters = null): array
    {
        if (!is_null($parameters)) {
            $text = $parameters->get(RequestParameterData::PROP_BODY)[RequestParameterData::PROP_STORAGE][RequestParameterData::PROP_VALUE];
        }

        return [
            IResponse::KEY_BODY => [
                IResponse::KEY_STORAGE => [
                    IResponse::KEY_VALUE => $text,
                ]]];
    }

    // Macro Code Specific

    /**
     * <strong>Do not change the method name!</strong>.
     *
     * @return string
     */
    protected static function projectdocpropertiesmarker(): string
    {
        return self::MACR_PDT_PROPERTIES_MARKER_01 . self::MACR_PH . self::MACR_PDT_PROPERTIES_MARKER_02;
    }

    // Misc Functions
}
