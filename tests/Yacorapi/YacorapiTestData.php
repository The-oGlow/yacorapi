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

use Ds\Collection;
use Ds\Map;
use oglow\tools\Addon\Atlassian\Extension\AtlassianExtension;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Extension\RapiClientExtension;
use oglow\tools\Yacorapi\Request\RequestParameterData;
use oglow\tools\Yacorapi\Space\SpaceTypeEnum;
use ollily\Tools\Test\TestData;

// @phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
class YacorapiTestData extends TestData
{
    /* Atlassian Extension '/

      /** All extensions */

    public const int EXTENSIONS_COUNT_TOTAL = 6;

    /** Array of extensions which will be verified */
    public const array EXTENSIONS_VERIFY = [RapiClientExtension::class, AtlassianExtension::class];

    /** Single extension which will be verified */
    public const array EXTENSION_VERIFY = [RapiClientExtension::class];

    public const int EXT_USER_MACRO_MACRO = 3;

    public const int EXT_USER_MACRO_ADDON = 1;

    public const int EXT_ATLASSIAN_ADDON = 2;

    public const int EXT_PDT_ADDON = 4;

    public const int EXT_3PARTY_MACRO = 69;

    public const int EXT_ATLASSIAN_MACRO = 60;

    public const int EXT_PDT_MACRO = 87;

    public const int EXT_3PARTY_ADDON = 11;

    // Atlassian Addon

    /** All addons from all extensions */
    public const int ADDONS_COUNT_TOTAL = 18;

    /** Names of one addon from each extension */
    public const array ADDONS_NAMES = [
        'Atlassian Confluence Macros',
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
        'projectdoc Toolbox',
    ];

    public const string CLAZZ_ALL_ADDON = '\oglow\tools\Yacorapi\Macro\AllAddon';

    public const string CLAZZ_SINGLE_ADDON = '\oglow\tools\Yacorapi\Macro\SingleAddon';

    public const string CLAZZ_PDT = '\oglow\tools\Addon\Projectdoc\Macro\ProjectdocAddon';

    public const string CLAZZ_3PARTY = '\oglow\tools\Addon\ThirdParty\Macro\ThirdPartyAddon';

    public const string CLAZZ_BLOCKER_ADDON = '\oglow\tools\Yacorapi\Macro\BlockerAddon';

    public const string CLAZZ_ATLASSIAN_ADDON = '\oglow\tools\Addon\Atlassian\Macro\AtlassianAddon';

    public const string CLAZZ_USER_MACRO_ADDON = '\oglow\tools\Addon\UserMacro\Macro\UserMacroAddon';

    /** The name of an addon which does not exists in the system */
    public const string ADDONS_NAME_NOTEXIST = 'NOTEXIST-ADDON';

    /** Array of specific addons which will be checked */
    public const array ADDONS_VERIFY = ['Atlassian Confluence Macros', 'Confluence User Macros'];

    /** Single addon which will be verified */
    public const string ADDON_VERIFY = 'Atlassian Confluence Macros';

    // (Addon-) Mode
    public const string MODE_NOTEXIST = 'NOTEXIST-MODE';

    public const int MODE_SINGLE_ADDON_COUNT_TOTAL = 1;

    public const int MODE_SINGLE_MACRO_COUNT_TOTAL = 5;

    public const string MODE_SINGLE_ADDON_NAME = 'single-addon';

    public const int MODE_SINGLE_ADDON_NAME_MACRO_COUNT = 5;

    public const string MODE_SINGLE_ADDON_NAME_NOTEXIST = self::ADDONS_NAME_NOTEXIST;

    public const int MODE_SINGLE_ADDON_NAME_NOTEXIST_MACRO_COUNT = 0;

    public const int MODE_BLOCKER_ADDON_COUNT_TOTAL = 11;

    public const int MODE_BLOCKER_MACRO_COUNT_TOTAL = 87;

    public const string MODE_BLOCKER_ADDON_NAME = 'Scroll Runtime for Confluence';

    public const int MODE_BLOCKER_ADDON_NAME_MACRO_COUNT = 7;

    public const string MODE_BLOCKER_ADDON_NAME_NOTEXISTS = self::ADDONS_NAME_NOTEXIST;

    public const int MODE_BLOCKER_ADDON_NAME_NOTEXISTS_MACRO_COUNT = 0;

    public const int MODE_ALL_ADDON_COUNT_TOTAL = 18;

    public const int MODE_ALL_MACRO_COUNT_TOTAL = 219;

    public const string MODE_ALL_ADDON_NAME = 'Scroll Documents for Confluence';

    public const int MODE_ALL_ADDON_NAME_MACRO_COUNT = 1;

    public const string MODE_ALL_ADDON_NAME_NOTEXIST = self::ADDONS_NAME_NOTEXIST;

    public const int MODE_ALL_ADDON_NAME_NOTEXIST_MACRO_COUNT = 0;

    // Atlassian Macro

    /** All macros from all addons */
    public const int MACROS_COUNT_TOTAL = 219;

    /** Array of specific macros which will be checked */
    public const array MACROS_VERIFY = ['children', 'code', 'create-from-template', 'section', 'toc'];

    /** Single macro which will be checked */
    public const string MACRO_VERIFY = 'create-from-template';

    // Unspecific test data
    public const string ADDON_1 = 'MyAddon';

    public const string ADDON_1_A = 'macro1';

    public const string ADDON_1_B = 'macro3';

    public const string ADDON_1_C = 'macro5';

    public const array ADDON_1_ORDER = [YacorapiTestData::ADDON_1_A, YacorapiTestData::ADDON_1_B, YacorapiTestData::ADDON_1_C];

    public const string ADDON_2 = 'OtherAddon';

    public const string ADDON_2_A = 'macro2';

    public const string ADDON_2_C = 'macro6';

    public const string ADDON_2_B = 'macro4';

    public const array ADDON_2_ORDER = [YacorapiTestData::ADDON_2_A, YacorapiTestData::ADDON_2_B, YacorapiTestData::ADDON_2_C];

    public const int C_RESPONSE_SIZE_EMPTY = 0;

    public const int C_PAGEID_NOTEXIST = -1;

    public const int C_PAGEID_EXIST = 123;

    public const int C_PAGEID_NEW = 11;

    public const ItemTypeEnum C_ITEM_TYPE_PAGE = ItemTypeEnum::PAGE;

    public const string C_SPACE_EMPTY = '';

    public const string C_SPACE_EXIST_KEY = 'SPCEX';

    public const int C_SPACE_EXIST_ID = 12345;

    public const string C_SPACE_EXIST_NAME = 'Existing Space';

    public const string C_SPACE_EXIST_DESCRIPTION = 'A space which exists.';

    public const string C_SPACE_EXIST_STATUS = RequestParameterData::VAL_STATUS_TYPE_CURRENT;

    public const SpaceTypeEnum C_SPACE_EXIST_TYPE = SpaceTypeEnum::SPACE_TYPE_GLOBAL;

    public const string C_FILTERTERM_01 = 'filter=1';

    public const string C_PREPURL_01 = 'title~Test';

    public const string C_SEARCHTERM_EMPTY = '';

    public const string C_SEARCHTERM_01 = 'searchtext';

    public const int C_SEARCHPAGEID_01 = 532951146;

    public const string C_SEARCHPAGETITLE_01 = 'SEARCHPAGETITLE_01';

    public const string C_SEARCHPAGESPACE_01 = self::C_SPACE_EXIST_KEY;

    public const int C_SEARCHPAGESPARENT_01 = self::C_PAGEID_EXIST;

    public const string HTML_PAGE = '<!DOCTYPE html><html><head><title>#pagetitle#</title></head><body></body></html>';

    // Macro Code
    public const string MACR_PH = '#ph#';

    public const string MACR_MACRO_ID = '12345678-1234-1234-1234-123456789012';

    public const string MACR_MACRO_TAG_START = '<ac:structured-macro ac:macro-id="' .
        self::MACR_MACRO_ID .
        '" ac:name="' .
        self::MACR_PH .
        '" ac:schema-version="1">';

    public const string MACR_MACRO_TAG_END = '</ac:structured-macro>';

    public const string MACR_BODY_EMPTY = '';

    public const string MACR_BODY_SIMPLE = '<body></body>';

    public const string MACR_BODY_INVALID = 'invalidBody';

    public const string MACR_BODY_CONTENT = 'Content of the macro body';

    // Macro Doctype
    public const string MACR_DOCTYPE_NEW = 'newDT';

    public const string MACR_DOCTYPE_OLD = 'oldDT';

    public const string MACR_DOCTYPE_WRONG = self::NOTEXIST_NAME;

    public const string MACR_DOCTYPE_EMPTY = '';
    // Macro Code Name

    public const string MACR_PROJECTDOC_PROPERTIES_MARKER = 'projectdoc-properties-marker';

    public const string MACR_PDT_PROPERTIES_MARKER_01 = '<ac:parameter ac:name="doctype">';

    public const string MACR_PDT_PROPERTIES_MARKER_02 = '</ac:parameter>';

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

    public const array RESP_RESTRICTION = [
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
            IResponse::KEY_TYPE => self::C_ITEM_TYPE_PAGE->value,
        ];
        self::$RESP_HEAD_SEARCHPAGEID_01 = array_merge(self::$RESP_HEAD_SEARCHPAGEID_01, self::prepareResponseVersion());
        self::$RESP_HEAD_SEARCHPAGEID_01 = array_merge(self::$RESP_HEAD_SEARCHPAGEID_01, self::prepareResponseSpace(self::C_SEARCHPAGESPACE_01, new Map()));
        self::$RESP_HEAD_SEARCHPAGEID_01 = array_merge(self::$RESP_HEAD_SEARCHPAGEID_01, self::prepareResponseAncestor(self::C_SEARCHPAGESPARENT_01, new Map()));

        return self::$RESP_HEAD_SEARCHPAGEID_01;
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function RESP_BODY(): array
    {
        self::$RESP_BODY =             self::prepareResponseBody(YacorapiTestData::HTML_PAGE, new Map());

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
     * @param string                  $text
     * @param Collection<mixed,mixed> $parameters
     *
     * @return array<mixed,mixed>
     */
    public static function prepareResponseSpace(string $text, Collection $parameters): array
    {
        /** @var Map<mixed,mixed> */
        $mapParameters = $parameters;

        if (!$mapParameters->isEmpty()) {
            $value = $mapParameters->get(RequestParameterData::PROP_SPACE);
            if (is_array($value) && count($value) > 0) {
                $text = $value[RequestParameterData::PROP_KEY];
            }
        }

        return [
            IResponse::KEY_SPACE => [
                IResponse::KEY_KEY => $text,
            ]];
    }

    /**
     * @param int $currentVersion
     *
     * @return array<mixed,mixed>
     */
    public static function prepareResponseVersion(int $currentVersion = 1): array
    {
        return [IResponse::KEY_VERSION => [IResponse::KEY_NUMBER => $currentVersion]];
    }

    /**
     * @param mixed                   $text
     * @param Collection<mixed,mixed> $parameters
     *
     * @return array<mixed,mixed>
     */
    public static function prepareResponseAncestor(mixed $text, Collection $parameters): array
    {
        /** @var Map<mixed,mixed> */
        $mapParameters = $parameters;

        if (!$mapParameters->isEmpty()) {
            $value = $mapParameters->get(RequestParameterData::PROP_ANCESTORS);
            if (is_array($value) && count($value) > 0) {
                $text = $value[0][RequestParameterData::PROP_ID];
            }
        }

        return [
            IResponse::KEY_ANCESTORS => [
                IResponse::KEY_ID => $text,
            ]];
    }

    /**
     * @param string                  $text
     * @param Collection<mixed,mixed> $parameters
     *
     * @return array<mixed,mixed>
     */
    public static function prepareResponseBody(string $text, Collection $parameters): array
    {
        /** @var Map<mixed,mixed> */
        $mapParameters = $parameters;

        if (!$mapParameters->isEmpty()) {
            $value = $mapParameters->get(RequestParameterData::PROP_BODY);
            if (is_array($value) && count($value) > 0) {
                $text = $value[RequestParameterData::PROP_STORAGE][RequestParameterData::PROP_VALUE];
            }
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
