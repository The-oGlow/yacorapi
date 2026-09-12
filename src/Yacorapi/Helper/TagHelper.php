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

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Ds\Sequence;
use Ds\Vector;
use Monolog\ConsoleLogger;
use Psr\Log\LoggerInterface;

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
     * Public constructor.
     *
     * @param string $key        Unique id of this singleton
     * @param bool   $withLogger TRUE=activate logging, else FALSE
     *
     * @phpstan-ignore constructor.unusedParameter,constructor.unusedParameter
     */
    public function __construct(string $key = '', bool $withLogger = true)
    {
        /** @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(TagHelper::class, level: static::LEVEL_DEFAULT);
        self::$logger->debug('START');

        parent::__construct(TagHelper::class);

        self::$logger->debug('END');
    }

    /**
     * Returns all tags with a specific tag name.
     *
     * @param string      $tagName The tag name
     * @param DOMDocument $domDoc  The dom structure to search in
     *
     * @return Sequence<mixed> All found tags
     */
    public static function getTag(string $tagName, DOMDocument $domDoc): Sequence
    {
        /** @var bool|DOMNodeList<DOMNode> */
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
     * Returns all tags with a specific tag name using {@link \DOMXPath}.
     *
     * @param string      $tagName The tag name
     * @param DOMDocument $domDoc  The dom structure to search in
     *
     * @return Sequence<mixed> All found tags
     */
    public static function findTag(string $tagName, DOMDocument $domDoc): Sequence
    {
        /** @var bool|DOMNodeList<DOMNode> */
        $result = false;
        if (!empty($tagName)) {
            try {
                $xpath = new DOMXPath($domDoc);
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
     * @param DOMDocument     $domDoc      The dom structure to remove in
     * @param Sequence<mixed> $deletedTags All deleted tags
     * @param bool            $allTags     TRUE=remove all found tags, FALSE=remove the first found tag
     *
     * @return DOMDocument The new dom structure
     */
    public static function deleteTag(string $tagName, DOMDocument $domDoc, Sequence &$deletedTags, bool $allTags = false): DOMDocument
    {
        /** @var bool|DOMNodeList<DOMNode> */
        $result = false;
        if (!empty($tagName)) {
            $foundTags = self::getTag($tagName, $domDoc);
            if ($allTags) {
                $result = [];
                foreach ($foundTags as $foundTag) {
                    try {
                        $result[] = $foundTag->parentNode->removeChild($foundTag);
                    } catch (\Throwable $error) {
                        self::$logger->warning($error->getMessage(), [$error::class]);
                    }
                }
            } else {
                if ($foundTags->count() > 0) {
                    $foundTag = $foundTags->first();

                    try {
                        $result = [$foundTag->parentNode->removeChild($foundTag)];
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
     * @param string         $tagNameSearch  The tag name to search for
     * @param DOMNode|string $tagNameReplace The tag name to replace with or the new DOMNode
     * @param DOMDocument    $domDoc         The dom structure to replace in
     *
     * @return DOMDocument The new dom structure
     */
    public static function replaceTags(string $tagNameSearch, string|DOMNode $tagNameReplace, DOMDocument $domDoc): DOMDocument
    {
        /** @var bool|DOMNodeList<DOMNode> */
        $result = false;
        if (!empty($tagNameSearch)) {
            $foundTags = self::getTag($tagNameSearch, $domDoc);
            $result = [];
            $i = $foundTags->count() - 1;
            while ($i > -1) {
                $foundTag = $foundTags->get($i);

                try {
                    if (empty($tagNameReplace)) {
                        $newTag = $domDoc->createTextNode($tagNameReplace);
                    } elseif ($tagNameReplace instanceof DOMNode) {
                        $newTag = $tagNameReplace;
                    } else {
                        $newTag = $domDoc->createElement($tagNameReplace);
                    }
                    $result[] = $foundTag->parentNode->replaceChild($newTag, $foundTag);
                } catch (\Throwable $error) {
                    self::$logger->warning($error->getMessage(), [$error::class]);
                }
                $i--;
            }
        }

        return $domDoc;
    }
}
