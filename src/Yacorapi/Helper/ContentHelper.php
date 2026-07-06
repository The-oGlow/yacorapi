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

class ContentHelper extends AbstractHelper
{
    public const string MACROBODY_PLAIN = 'html;';

    public const string MACROBODY_RICHTEXT = 'section;column;panel;';

    public const string CHOOSE_BODY_RICHTEXT = 'rich';

    public const string CHOOSE_BODY_PLAIN = 'plain';

    public const string TAG_PARAM_START = "\n<ac:parameter ac:name=\"%s\">";

    public const string TAG_PARAM_END = "</ac:parameter>";

    public const string TAG_PLAIN_START = "<ac:plain-text-body><![CDATA[";

    public const string TAG_PLAIN_END = "]]></ac:plain-text-body>";

    public const string TAG_RICH_START = "\n<ac:rich-text-body>";

    public const string TAG_RICH_END = "</ac:rich-text-body>";

    public const string TAG_MACRO_START = "<ac:structured-macro ac:name=\"%s\" ac:schema-version=\"%s\">\n";

    public const string TAG_MACRO_END = "\n</ac:structured-macro>";

    public const string TAG_MACRO_VERSION = "1";

    private static LoggerInterface $logger;

    public function __construct(bool $withLogger = true)
    {
        self::$logger = new ConsoleLogger(ContentHelper::class);
        self::$logger->debug('START');

        parent::__construct(ContentHelper::class, $withLogger);

        self::$logger->debug('END');
    }

    /**
     * @param null|Map<string,string> $parameters
     *
     * @return string
     */
    public static function prepareMacroParameter(?Map $parameters = null): string
    {
        $newTag = '';
        if (!is_null($parameters) && !$parameters->isEmpty()) {
            foreach ($parameters as $item => $value) {
                $newTag .= sprintf(self::TAG_PARAM_START . '%s' . self::TAG_PARAM_END, $item, $value);
            }
        }

        return $newTag;
    }

    public static function preparePlainBody(string $body = ''): string
    {
        $newTag = '';
        if (!empty($body)) {
            $newTag = sprintf(self::TAG_PLAIN_START . '%s' . self::TAG_PLAIN_END, $body);
        }

        return $newTag;
    }

    public static function prepareRichTextBody(string $body = ''): string
    {
        $newTag = '';
        if (!empty($body)) {
            $newTag = sprintf(self::TAG_RICH_START . '%s' . self::TAG_RICH_END, $body);
        }

        return $newTag;
    }

    public static function prepareMacroBody(string $macroName, string $body = ''): string
    {
        $newTag = '';
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

    public static function chooseMacroBody(string $macroName = ''): string
    {
        $choose = '';
        switch (true) {
            case str_contains(self::MACROBODY_PLAIN, strtolower($macroName . ';')):
                $choose = self::CHOOSE_BODY_PLAIN;
                break;
            case str_contains(self::MACROBODY_RICHTEXT, strtolower($macroName . ';')):
                $choose = self::CHOOSE_BODY_RICHTEXT;
                break;
            default:
        }

        return $choose;
    }

    /**
     * @param string                  $macroName
     * @param null|Map<string,string> $parameters
     * @param string                  $body
     *
     * @return string
     */
    public static function prepareMacro(string $macroName, ?Map $parameters = null, string $body = ''): string
    {
        $newTag = '';
        $newTag .= sprintf(self::TAG_MACRO_START, $macroName, self::TAG_MACRO_VERSION);
        $newTag .= self::prepareMacroParameter($parameters);
        $newTag .= self::prepareMacroBody($macroName, $body);
        $newTag .= self::TAG_MACRO_END;

        return $newTag;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareSettings(): void
    {
        // NothingToDo
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    final protected function validateSettings(Map $overrideParameters): bool
    {
        return true;
    }
}
