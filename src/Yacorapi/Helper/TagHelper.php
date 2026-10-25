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

use Ds\Seq;
use InvalidArgumentException;
use Monolog\DoNothingLogger;
use Monolog\EasyGoingLogger;
use ollily\Common\AbstractHelper;
use Psr\Log\LoggerInterface;
use voku\helper\XmlDomParser;

/**
 * Helper clazz for editing tags of a confluence page.
 *
 * @author ollily
 */
class TagHelper extends AbstractHelper
{
    public const string NAMESPACE_SEP = ':';

    private static LoggerInterface $logger;

    /**
     * Protected constructor.
     *
     * @param bool       $withLogger TRUE=activate logging, else FALSE
     * @param int|string $level      The minimum logging level at which this handler will be triggered (Default: {@link self::LEVEL_DEFAULT})
     */
    protected function __construct(bool $withLogger = true, int|string $level = self::LEVEL_DEFAULT)
    {
        if ($withLogger) {
            self::$logger = EasyGoingLogger::init(TagHelper::class, level: $level);
        } else {
            self::$logger = new DoNothingLogger();
        }
        self::$logger->debug('START');

        parent::__construct($withLogger, $level);

        self::$logger->debug('END');
    }

    /**
     * Returns all tags with a specific tag name.
     *
     * @param string                   $tagName  The tag name
     * @param \DOMDocument|\DOMElement $domDoc   The dom structure to search in
     * @param string                   $nsPrefix
     *
     * @return Seq All found tags
     */
    public static function getTag(string $tagName, \DOMElement|\DOMDocument $domDoc, string $nsPrefix = ''): Seq
    {
        $result = [];
        if (!empty($tagName)) {
            if (!empty($nsPrefix)) {
                $result = $domDoc->getElementsByTagNameNS($nsPrefix, $tagName);
            } else {
                $result = $domDoc->getElementsByTagName($tagName);
            }
        }

        return new Seq($result);
    }

    /**
     * Returns all tags with a specific tag name using {@link XmlDomParser} or {@link \DOMXPath}.
     *
     * @param string       $tagName   The tag name
     * @param \DOMDocument $domDoc    The dom structure to search in
     * @param string       $nsPrefix  The namespace prefix to register (Default: '')
     * @param string       $nsUri     The namespace uri to register (Default: '')
     * @param bool         $withXPath TRUE=Use XPath to find, else use SimpleHtmlDom (Default: FALSE)
     *
     * @return Seq All found tags
     */
    public static function findTag(string $tagName, \DOMDocument $domDoc, string $nsPrefix = '', string $nsUri = '', bool $withXPath = false): Seq
    {
        $result = [];
        if (!empty($tagName)) {
            try {
                if ($withXPath) {
                    $xpath = new \DOMXPath($domDoc);
                    if (!empty($nsPrefix)) {
                        $xpath->registerNamespace($nsPrefix, $nsUri);
                    }
                    if (str_contains($tagName, self::NAMESPACE_SEP)) {
                        $result = $xpath->query($tagName, registerNodeNS: true);
                    } else {
                        $result = $xpath->query($tagName);
                    }
                } else {
                    $simpleXmlDom = new XmlDomParser($domDoc);
                    $simpleXmlDom->autoRegisterXPathNamespaces(true);
                    /**
                     * Method has too many params.
                     *
                     * @psalm-suppress TooManyTemplateParams
                     */
                    $result = $simpleXmlDom->findMultiOrNull($tagName);
                    // Prevent memory leak
                    unset($simpleXmlDom);
                }
            } catch (\Throwable $error) {
                self::$logger->notice($error->getMessage(), [$error::class]);
            }
        }
        if (is_bool($result) || is_null($result)) {
            $result = [];
        }

        return new Seq($result);
    }

    /**
     * Removes the first found or all tags with a specific tag name.
     *
     * @param string       $tagName     The tag name
     * @param \DOMDocument $domDoc      The dom structure to remove in
     * @param Seq          $deletedTags All deleted tags
     * @param bool         $allTags     TRUE=remove all found tags, FALSE=remove the first found tag
     *
     * @return \DOMDocument The new dom structure
     */
    public static function deleteTag(string $tagName, \DOMDocument $domDoc, Seq &$deletedTags, bool $allTags = false): \DOMDocument
    {
        $result = [];
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
        $deletedTags = new Seq($result);

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
        $result = [];
        if (!empty($tagNameSearch)) {
            $foundTags = self::getTag($tagNameSearch, $domDoc);
            $posIdx = $foundTags->count() - 1;
            while ($posIdx > -1) {
                /** @var \DOMElement $foundTag */
                $foundTag = $foundTags->get($posIdx);

                try {
                    if (is_string($tagNameReplace) && empty($tagNameReplace)) {
                        $newTag = $domDoc->createTextNode($tagNameReplace);
                    } elseif (is_string($tagNameReplace)) {
                        $newTag = $domDoc->createElement($tagNameReplace);
                        if (is_bool($newTag)) {
                            throw new InvalidArgumentException(sprintf("Invalid tagname replacement '%s'", $tagNameReplace));
                        }
                    } else {
                        $newTag = $tagNameReplace;
                    }
                    if (!is_null($foundTag->parentNode)) {
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
