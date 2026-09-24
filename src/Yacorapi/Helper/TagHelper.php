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

use Ds\Sequence;
use Ds\Vector;
use Monolog\ConsoleLogger;
use ollily\Common\AbstractHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Helper clazz for editing tags of a confluence page.
 *
 * @author ollily
 *
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
class TagHelper extends AbstractHelper
{
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
        self::$logger = new ConsoleLogger(TagHelper::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($withLogger, $level);

        self::$logger->debug('END');
    }

    /**
     * Returns all tags with a specific tag name.
     *
     * @param string       $tagName The tag name
     * @param \DOMDocument $domDoc  The dom structure to search in
     *
     * @return Sequence<mixed> All found tags
     */
    public static function getTag(string $tagName, \DOMDocument $domDoc): Sequence
    {
        /** @psalm-suppress TooManyTemplateParams
         *  @var bool|\DOMNodeList<\DOMNameSpaceNode|\DOMNode> */
        $result = false;
        if (!empty($tagName)) {
            $result = $domDoc->getElementsByTagName($tagName);
        }
        if (!is_bool($result)) {
            $tags = new Vector($result);
        } else {
            $tags = new Vector();
        }

        return $tags;
    }

    /**
     * Returns all tags with a specific tag name using {@link \\DOMXPath}.
     *
     * @param string       $tagName The tag name
     * @param \DOMDocument $domDoc  The dom structure to search in
     *
     * @return Sequence<mixed> All found tags
     */
    public static function findTag(string $tagName, \DOMDocument $domDoc): Sequence
    {
        /** @psalm-suppress TooManyTemplateParams
         *  @var bool|\DOMNodeList<\DOMNameSpaceNode|\DOMNode> */
        $result = false;
        if (!empty($tagName)) {
            try {
                $xpath = new \DOMXPath($domDoc);
                $result = $xpath->query($tagName);
            } catch (\Throwable $error) {
                self::$logger->notice($error->getMessage(), [$error::class]);
            }
        }
        if (!is_bool($result)) {
            $tags = new Vector($result);
        } else {
            $tags = new Vector();
        }

        return $tags;
    }

    /**
     * Removes the first found or all tags with a specific tag name.
     *
     * @param string          $tagName     The tag name
     * @param \DOMDocument    $domDoc      The dom structure to remove in
     * @param Sequence<mixed> $deletedTags All deleted tags
     * @param bool            $allTags     TRUE=remove all found tags, FALSE=remove the first found tag
     *
     * @return \DOMDocument The new dom structure
     */
    public static function deleteTag(string $tagName, \DOMDocument $domDoc, Sequence &$deletedTags, bool $allTags = false): \DOMDocument
    {
        /** @psalm-suppress TooManyTemplateParams
         *  @var bool|\DOMNodeList<\DOMNameSpaceNode|\DOMNode> */
        $result = false;
        if (!empty($tagName)) {
            $foundTags = self::getTag($tagName, $domDoc);
            if ($allTags) {
                $result = [];
                /** @var \DOMElement $foundTag */
                foreach ($foundTags as $foundTag) {
                    try {
                        if (!is_null($foundTag->parentNode)) {
                            $result[] = $foundTag->parentNode->removeChild($foundTag);
                        }
                    } catch (\Throwable $error) {
                        self::$logger->warning($error->getMessage(), [$error::class]);
                    }
                }
            } else {
                if ($foundTags->count() > 0) {
                    /** @var \DOMElement $foundTag */
                    $foundTag = $foundTags->first();

                    try {
                        if (!is_null($foundTag->parentNode)) {
                            $result = [$foundTag->parentNode->removeChild($foundTag)];
                        }
                    } catch (\Throwable $error) {
                        self::$logger->warning($error->getMessage(), [$error::class]);
                    }
                }
            }
        }
        if (!is_bool($result)) {
            $deletedTags = new Vector($result);
        } else {
            $deletedTags = new Vector();
        }

        return $domDoc;
    }

    /**
     * @param string          $tagNameSearch  The tag name to search for
     * @param \DOMNode|string $tagNameReplace The tag name to replace with or the new \DOMNode
     * @param \DOMDocument    $domDoc         The dom structure to replace in
     *
     * @return \DOMDocument The new dom structure
     */
    public static function replaceTags(string $tagNameSearch, \DOMNode|string $tagNameReplace, \DOMDocument $domDoc): \DOMDocument
    {
        /** @psalm-suppress TooManyTemplateParams
         *  @var bool|\DOMNodeList<\DOMNameSpaceNode|\DOMNode> */
        $result = false;
        if (!empty($tagNameSearch)) {
            $foundTags = self::getTag($tagNameSearch, $domDoc);
            $result = [];
            $posIdx = $foundTags->count() - 1;
            while ($posIdx > -1) {
                /** @var \DOMElement $foundTag */
                $foundTag = $foundTags->get($posIdx);

                try {
                    /** @psalm-suppress RedundantCondition */
                    if (is_string($tagNameReplace) && empty($tagNameReplace)) {
                        $newTag = $domDoc->createTextNode($tagNameReplace);
                    } elseif (is_string($tagNameReplace)) {
                        $newTag = $domDoc->createElement($tagNameReplace);
                    } elseif ($tagNameReplace instanceof \DOMNode) { // @phpstan-ignore instanceof.alwaysTrue
                        $newTag = $tagNameReplace;
                    } else {
                        $newTag = '';
                    }
                    if (!is_null($foundTag->parentNode) && $newTag instanceof \DOMNode) {
                        $result[] = $foundTag->parentNode->replaceChild($newTag, $foundTag);
                    }
                } catch (\Throwable $error) {
                    self::$logger->warning($error->getMessage(), [$error::class]);
                }
                $posIdx--;
            }
        }

        return $domDoc;
    }
}
