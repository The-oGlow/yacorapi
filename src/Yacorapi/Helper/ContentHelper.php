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
    public const MACROBODY_PLAIN = 'html;';

    public const MACROBODY_RICHTEXT = 'section;column;';

    public const CHOOSE_BODY_RICHTEXT = 'rich';

    public const CHOOSE_BODY_PLAIN = 'plain';

    public const TAG_PARAM_START = "\n<ac:parameters ac:name=\"%s\">";

    public const TAG_PARAM_END = "</ac:parameters>";

    public const TAG_PLAIN_START = "<ac:plain-text-body><![CDATA[";

    public const TAG_PLAIN_END = "]]></ac:plain-text-body>";

    public const TAG_RICH_START = "<ac:rich-text-body>\n";

    public const TAG_RICH_END = "\n</ac:rich-text-body>";

    public const TAG_MACRO_START = "<ac:structured-macro ac:name=\"%s\" ac:schema-version=\"%s\">\n";

    public const TAG_MACRO_END = "\n</ac:structured-macro>";

    public const TAG_MACRO_VERSION = "1";

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
    public function prepareMacroParameter(?Map $parameters = null): string
    {
        $newTag = '';
        if (!is_null($parameters) && !$parameters->isEmpty()) {
            foreach ($parameters as $item => $value) {
                $newTag .= sprintf(self::TAG_PARAM_START . '%s' . self::TAG_PARAM_END, $item, $value);
            }
        }

        return $newTag;
    }

    public function preparePlainBody(string $body = ''): string
    {
        $newTag = '';
        if (!empty($body)) {
            $newTag = sprintf(self::TAG_PLAIN_START . '%s' . self::TAG_PLAIN_END, $body);
        }

        return $newTag;
    }

    public function prepareRichTextBody(string $body = ''): string
    {
        $newTag = '';
        if (!empty($body)) {
            $newTag = sprintf(self::TAG_RICH_START . '%s' . self::TAG_RICH_END, $body);
        }

        return $newTag;
    }

    public function prepareMacroBody(string $macroName, string $body = ''): string
    {
        $newTag = '';
        if (!empty($body)) {
            switch ($this->chooseMacroBody($macroName)) {
                case self::CHOOSE_BODY_PLAIN:
                    $newTag .= sprintf("%s", $this->preparePlainBody($body));
                    break;
                case self::CHOOSE_BODY_RICHTEXT:
                    $newTag .= sprintf("%s", $this->prepareRichTextBody($body));
                    break;
                default:
                    $newTag .= $body;
            }
        }

        return $newTag;
    }

    public function chooseMacroBody(string $macroName = ''): string
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
    public function prepareMacro(string $macroName, ?Map $parameters = null, string $body = ''): string
    {
        $newTag = '';
        $newTag .= sprintf(self::TAG_MACRO_START, $macroName, self::TAG_MACRO_VERSION);
        $newTag .= $this->prepareMacroParameter($parameters);
        $newTag .= $this->prepareMacroBody($macroName, $body);
        $newTag .= self::TAG_MACRO_END;

        return $newTag;
    }

    protected function prepareSettings(): void
    {
        // NothingToDo
    }

    /**
     * @param Map<mixed, mixed> $overrideParameters
     *
     * @return bool
     */
    final protected function validateSettings(Map $overrideParameters): bool
    {
        return true;
    }
}
