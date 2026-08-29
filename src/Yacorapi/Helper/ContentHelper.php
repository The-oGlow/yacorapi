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
use Monolog\ConsoleLogger;
use Psr\Log\LoggerInterface;

class ContentHelper extends AbstractHelper {

    /** List of macros having a plain body */
    public const string MACROBODY_PLAIN = 'html;';
    /** List of macros having a rich body */
    public const string MACROBODY_RICHTEXT = 'section;column;panel;';
    /** Selection custom body for macro */
    public const string CHOOSE_BODY_CUSTOM = 'custom';
    /** Selection plain body for macro */
    public const string CHOOSE_BODY_PLAIN = 'plain';
    /** Selection rich body for macro */
    public const string CHOOSE_BODY_RICHTEXT = 'rich';
    /** Start tag for macro  */
    public const string TAG_MACRO_START = "<ac:structured-macro ac:name=\"%s\" ac:schema-version=\"%s\">\n";

    /** END tag for macro */
    public const string TAG_MACRO_END = "\n</ac:structured-macro>";

    /** Tag for macro version */
    public const string TAG_MACRO_VERSION = "1";

    /** Start tag for macro parameter */
    public const string TAG_PARAM_START = "\n<ac:parameter ac:name=\"%s\">";
    /** End tag for macro parameter */
    public const string TAG_PARAM_END = "</ac:parameter>";
    /** Start tag for plain body */
    public const string TAG_PLAIN_START = "<ac:plain-text-body><![CDATA[";
    /** End tag for plain body */
    public const string TAG_PLAIN_END = "]]></ac:plain-text-body>";
    /** Start tag for rich body */
    public const string TAG_RICH_START = "\n<ac:rich-text-body>";
    /** End tag for rich body */
    public const string TAG_RICH_END = "</ac:rich-text-body>";
    /** The initial content for a macro body */
    public const string BODY_EMPTY= '';
    /** The initial content for a macro tag */
    public const string TAG_EMPTY = '';

    private static LoggerInterface $logger;

    public function __construct(bool $withLogger = true) {
        self::$logger = new ConsoleLogger(ContentHelper::class);
        self::$logger->debug('START');

        parent::__construct(ContentHelper::class, $withLogger);

        self::$logger->debug('END');
    }

    /**
     * Creates the tag for a confluence macro including parameters (optional) and body (optional):
     * 
     * @param string             $macroName The name of the confluence macro as it shown in the source view
     * @param Map<string,string> $parameters A map of parameters (optional)
     * @param string             $body The content of the body of the macro (optional)
     *
     * @return string The full tag for the macro
     */
    public static function prepareMacro(string $macroName, Map $parameters, string $body = self::BODY_EMPTY): string {
        $newTag = self::TAG_EMPTY;
        $newTag .= sprintf(self::TAG_MACRO_START, $macroName, self::TAG_MACRO_VERSION);
        $newTag .= self::prepareMacroParameter($parameters);
        $newTag .= self::prepareMacroBody($macroName, $body);
        $newTag .= self::TAG_MACRO_END;

        return $newTag;
    }

    /**
     * Creates the tags for the parameter in the macro. If map is empty, nothing will be created.
     * 
     * @param Map<string,string> $parameters
     *
     * @return string The tags of the macro parameters
     */
    public static function prepareMacroParameter(Map $parameters): string {
        $newTag = self::TAG_EMPTY;
        if (!$parameters->isEmpty()) {
            foreach ($parameters as $item => $value) {
                $newTag .= sprintf(self::TAG_PARAM_START . '%s' . self::TAG_PARAM_END, $item, $value);
            }
        }

        return $newTag;
    }

    /**
     * Creates the tag for the body in the macro. Dependent on the macro, a plain body tag or a rich body tag is used.
     * 
     * @param string             $macroName The name of the confluence macro as it shown in the source view
     * @param string             $body The content of the body of the macro (optional)
     * @return string The tag for the macro body
     */
    public static function prepareMacroBody(string $macroName, string $body = self::BODY_EMPTY): string {
        $newTag = self::TAG_EMPTY;
        if (!empty($body)) {
            switch (self::chooseMacroBody($macroName)) {
                case self::CHOOSE_BODY_PLAIN:
                    $newTag .= sprintf("%s", self::preparePlainBody($body));
                    break;
                case self::CHOOSE_BODY_RICHTEXT:
                    $newTag .= sprintf("%s", self::prepareRichTextBody($body));
                    break;
                default:
                    $newTag .= $body;
            }
        }

        return $newTag;
    }

    /**
     * Dependent on the macro, a plain body tag or a rich body tag is used.
     * 
     * @param string             $macroName The name of the confluence macro as it shown in the source view
     * @return string The tag for the macro body
     */
    public static function chooseMacroBody(string $macroName = ''): string {
        $choose = '';
        switch (true) {
            case str_contains(self::MACROBODY_PLAIN, strtolower($macroName . ';')):
                $choose = self::CHOOSE_BODY_PLAIN;
                break;
            case str_contains(self::MACROBODY_RICHTEXT, strtolower($macroName . ';')):
                $choose = self::CHOOSE_BODY_RICHTEXT;
                break;
            default:
                $choose = self::CHOOSE_BODY_CUSTOM;
                break;
        }

        return $choose;
    }

    /**
     * Creates a tag with a plain body tag.
     * 
     * @param string             $body The content of the body of the macro (optional)
     * @return string The plain body tag
     */
    public static function preparePlainBody(string $body = self::BODY_EMPTY): string {
        $newTag = self::TAG_EMPTY;
        if (!empty($body)) {
            $newTag = sprintf(self::TAG_PLAIN_START . '%s' . self::TAG_PLAIN_END, $body);
        }

        return $newTag;
    }

    /**
     * 
     * @param string             $body The content of the body of the macro (optional)
     * @return string The rich body tag
     */
    public static function prepareRichTextBody(string $body = self::BODY_EMPTY): string {
        $newTag = self::TAG_EMPTY;
        if (!empty($body)) {
            $newTag = sprintf(self::TAG_RICH_START . '%s' . self::TAG_RICH_END, $body);
        }

        return $newTag;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareSettings(Map $overrideParameters): void {
        // NothingToDo
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function validateSettings(Map $overrideParameters): bool {
        return true;
    }
}
