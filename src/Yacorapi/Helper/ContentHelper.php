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

use Ds\Collection;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\Macro\HasMacroBodyEnum;
use ollily\Common\AbstractHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Helper clazz for generating content for a confluence page.
 *
 * @author ollily
 *
 * @phpstan-type HeaderLevelType 1|2|3|4|5|6
 *
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
class ContentHelper extends AbstractHelper
{
    /** @var string Start tag for macro */
    public const string TAG_MACRO_START = '<ac:structured-macro ac:name="%s" ac:schema-version="%s">';

    /** @var string End tag for macro */
    public const string TAG_MACRO_END = '</ac:structured-macro>';

    /** @var string Tag for macro version */
    public const string TAG_MACRO_VERSION = '1';

    /** @var string Tag for macro parameter */
    public const string TAG_PARAMETER = '<ac:parameter ac:name="%s">%s</ac:parameter>';

    /** @var string Start tag for plain body */
    public const string TAG_BODY_PLAIN = '<ac:plain-text-body><![CDATA[%s]]></ac:plain-text-body>';

    /** @var string Tag for rich body */
    public const string TAG_BODY_RICH = '<ac:rich-text-body>%s</ac:rich-text-body>';

    /** @var string The initial content for a macro body */
    public const string VAL_BODY_EMPTY = '';

    /** @var string The initial content for a macro tag */
    public const string VAL_TAG_EMPTY = '';

    private static LoggerInterface $logger;

    /**
     * Protected constructor.
     *
     * @param bool                   $withLogger TRUE=activate logging, else FALSE
     * @param int|LogLevel::*|string $level      The minimum logging level at which this handler will be triggered (Default: {@link self::LEVEL_DEFAULT})
     */
    protected function __construct(bool $withLogger = true, LogLevel|string|int $level = self::LEVEL_DEFAULT)
    {
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(ContentHelper::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($withLogger, $level);

        self::$logger->debug('END');
    }

    /**
     * Creates the tag for a confluence macro including parameters (optional) and body (optional):
     *
     * @param string                    $macroName  The name of the confluence macro as it shown in the source view
     * @param Collection<string,string> $parameters A map of parameters (optional)
     * @param string                    $body       The content of the body of the macro (Default {@link self::VAL_BODY_EMPTY})
     *
     * @return string The full tag for the macro
     *
     * @see self::VAL_BODY_EMPTY
     */
    public static function prepareMacro(string $macroName, Collection $parameters, string $body = self::VAL_BODY_EMPTY): string
    {
        $newTag = self::VAL_TAG_EMPTY;
        $newTag .= sprintf(self::TAG_MACRO_START, $macroName, self::TAG_MACRO_VERSION);
        $newTag .= self::prepareMacroParameter($parameters);
        $newTag .= self::prepareMacroBody($macroName, $body);
        $newTag .= self::TAG_MACRO_END;

        return $newTag;
    }

    /**
     * Creates the tags for the parameter in the macro. If map is empty, nothing will be created.
     *
     * @param Collection<string,string> $parameters
     *
     * @return string The tags of the macro parameters
     */
    public static function prepareMacroParameter(Collection $parameters): string
    {
        $newTag = self::VAL_TAG_EMPTY;
        if (!$parameters->isEmpty()) {
            foreach ($parameters as $paramName => $paramValue) {
                //                $newTag .= sprintf(self::TAG_PARAM_START . '%s' . self::TAG_PARAM_END, $item, $value);
                $newTag .= sprintf(self::TAG_PARAMETER, $paramName, $paramValue);
            }
        }

        return $newTag;
    }

    /**
     * Creates the tag for the body in the macro. Dependent on the macro, a plain body tag or a rich body tag is used.
     *
     * @param string $macroName The name of the confluence macro as it shown in the source view
     * @param string $body      The content of the body of the macro (Default {@link self::VAL_BODY_EMPTY})
     *
     * @return string The tag for the macro body
     *
     * @see self::VAL_BODY_EMPTY
     */
    public static function prepareMacroBody(string $macroName, string $body = self::VAL_BODY_EMPTY): string
    {
        $newTag = self::VAL_TAG_EMPTY;

        if (!empty($body)) {
            switch (self::chooseMacroBody($macroName)) {
                case HasMacroBodyEnum::PLAIN:
                    $newTag .= self::preparePlainBody($body);
                    break;
                case HasMacroBodyEnum::RICH:
                    $newTag .=  self::prepareRichTextBody($body);
                    break;
                case HasMacroBodyEnum::CUSTOM:
                    $newTag .= $body;
                    break;
                default:
                    break;
            }
        }

        return $newTag;
    }

    /**
     * Dependent on the macro, the mode for a plain or a rich body tag is chosen. If macro not defined or empty the custom mode is chosen.
     *
     * @param string $macroName The name of the confluence macro as it shown in the source view (Default {@link self::VAL_TAG_EMPTY})
     *
     * @return HasMacroBodyEnum The mode for the macro body
     *
     * @see HasMacroBodyEnum
     * @see self::VAL_TAG_EMPTY
     */
    public static function chooseMacroBody(string $macroName = self::VAL_TAG_EMPTY): HasMacroBodyEnum
    {
        return HasMacroBodyEnum::hasBody($macroName);
    }

    /**
     * Creates a tag with a plain body tag.
     *
     * @param string $body The content of the body of the macro (Default {@link self::VAL_BODY_EMPTY})
     *
     * @return string The plain body tag
     *
     * @see self::VAL_BODY_EMPTY
     */
    public static function preparePlainBody(string $body = self::VAL_BODY_EMPTY): string
    {
        $newTag = self::VAL_TAG_EMPTY;
        if (!empty($body)) {
            $newTag = sprintf(self::TAG_BODY_PLAIN, $body);
        }

        return $newTag;
    }

    /**
     * Creates a tag with a rich body tag.
     *
     * @param string $body The content of the body of the macro (Default {@link self::VAL_BODY_EMPTY})
     *
     * @return string The rich body tag
     *
     * @see self::VAL_BODY_EMPTY
     */
    public static function prepareRichTextBody(string $body = self::VAL_BODY_EMPTY): string
    {
        $newTag = self::VAL_TAG_EMPTY;
        if (!empty($body)) {
            $newTag = sprintf(self::TAG_BODY_RICH, $body);
        }

        return $newTag;
    }

    /**
     * Creates a &lt;h*&gt;-tag with text.
     *
     * @param string $text        The text used as heading
     * @param int    $headerLevel the level for the H*-tag (Default: 1)
     *
     * @phpstan-param HeaderLevelType $headerLevel
     *
     * @return string H*-tag
     */
    public static function prepareHeading(string $text, int $headerLevel = 1): string
    {
        return sprintf('<h%s>%s</h%s>', $headerLevel, $text, $headerLevel);
    }
}
