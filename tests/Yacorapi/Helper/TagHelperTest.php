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
use Ds\Vector;
use oglow\tools\Yacorapi\YacorapiTestData as YTD;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\EasyGoingTestCase;

/**
 * @author ollily
 */
class TagHelperTest extends EasyGoingTestCase
{
    #[\Override]
    protected function getCasto2t(): TagHelper
    {
        return $this->o2t;
    }

    #[\Override]
    protected static function prepareO2t(): object
    {
        return TagHelper::i();
    }

    /**
     * @param int         $expectedCount
     * @param string      $expectedTagName
     * @param string      $tagName
     * @param DOMDocument $domDoc
     */
    #[DataProvider('providerGetTagFindTag')]
    public function testGetTag(int $expectedCount, string $expectedTagName, string $tagName, DOMDocument $domDoc): void
    {
        try {
            $actual = $this->getCasto2t()::getTag($tagName, $domDoc);
        } catch (\Throwable $thrown) {
            self::fail(sprintf('%s - %s', $thrown->getMessage(), $thrown::class));
        }
        $this->validFindResults($expectedCount, $expectedTagName, $actual);
    }

    /**
     * @param int         $expectedCount
     * @param string      $expectedTagName
     * @param string      $tagName
     * @param DOMDocument $domDoc
     */
    #[DataProvider('providerGetTagFindTag')]
    public function testfindTag(int $expectedCount, string $expectedTagName, string $tagName, DOMDocument $domDoc): void
    {
        try {
            $actual = $this->getCasto2t()::findTag($tagName, $domDoc);
        } catch (\Throwable $thrown) {
            self::fail(sprintf('%s - %s', $thrown->getMessage(), $thrown::class));
        }
        $this->validFindResults($expectedCount, $expectedTagName, $actual);
    }

    /**
     * @param int         $expectedCount
     * @param string      $tagName
     * @param DOMDocument $domDoc
     * @param bool        $allTags
     */
    #[DataProvider('providerDeleteTag')]
    public function testDeleteTag(int $expectedCount, string $tagName, DOMDocument $domDoc, bool $allTags): void
    {
        $occurenceBefore = 0;
        if (!empty($tagName)) {
            $xmlCode = $domDoc->saveXML();
            if (!is_bool($xmlCode)) {
                $occurenceBefore = substr_count($xmlCode, $tagName);
            }
        }

        $deletedTags = new Vector();

        try {
            $actual = $this->getCasto2t()::deleteTag($tagName, $domDoc, $deletedTags, $allTags);
            self::assertEquals($domDoc, $actual);
        } catch (\Throwable $thrown) {
            self::fail(sprintf('%s - %s', $thrown->getMessage(), $thrown::class));
        }

        $this->validFindResults($expectedCount, $tagName, $deletedTags);

        $occurenceAfter = 0;
        if (!empty($tagName)) {
            $xmlCode = $domDoc->saveXML();
            if (!is_bool($xmlCode)) {
                $occurenceAfter = substr_count($xmlCode, $tagName);
            }
        }

        if ($allTags) {
            self::assertEquals(0, $occurenceAfter);
        } else {
            if ($occurenceBefore > 1) {
                self::assertGreaterThanOrEqual(1, $occurenceAfter);
            } else {
                self::assertEquals(0, $occurenceAfter);
            }
        }
    }

    /**
     * @param int            $expectedCount
     * @param string         $tagNameSearch
     * @param DOMNode|string $tagNameReplace
     * @param DOMDocument    $domDoc
     */
    #[DataProvider('providerReplaceTags')]
    public function testReplaceTags(int $expectedCount, string $tagNameSearch, string|DOMNode $tagNameReplace, DOMDocument $domDoc): void
    {
        $actual = $this->getCasto2t()::replaceTags($tagNameSearch, $tagNameReplace, $domDoc);
        self::assertEquals($domDoc, $actual);

        $occurenceAfter = 0;
        if (!empty($tagNameSearch)) {
            $xmlCode = $domDoc->saveXML();
            if (!is_bool($xmlCode)) {
                $occurenceAfter = substr_count($xmlCode, $tagNameSearch);
            }
        }

        self::assertEquals($expectedCount, $occurenceAfter);
    }

    public function validFindResults(int $expectedCount, string $expectedTagName, mixed $actual): void
    {
        self::assertCount($expectedCount, $actual);
        if ($expectedCount > 0 && $actual->count() > 0) {
            foreach ($actual as $element) {
                self::assertEquals($expectedTagName, $element->tagName);
            }
        } elseif ($expectedCount > 0 && $actual->count() <= 0) {
            self::fail(sprintf('Expected %s result, but there is 0 result', $expectedCount));
        } elseif ($expectedCount <= 0 && $actual->count() > 0) {
            self::fail(sprintf('Expected 0 result, but there is %s result', $actual->count()));
        }
    }

    // Dataprovider

    private static function emptyDom(): DOMDocument
    {
        return YTD::prepareDOMDocument(YTD::TAG_EMPTY);
    }

    private static function wrongDom(): DOMDocument
    {
        return YTD::prepareDOMDocument(sprintf(YTD::TAG_ROOT, '', YTD::TAG_WRONG));
    }

    private static function existDom(): DOMDocument
    {
        return YTD::prepareDOMDocument(sprintf(YTD::TAG_ROOT, '', YTD::TAG_EXIST));
    }

    private static function wrongDomMultiple(): DOMDocument
    {
        return YTD::prepareDOMDocument(sprintf(YTD::TAG_ROOT, '', YTD::TAG_WRONG . YTD::TAG_WRONG . YTD::TAG_EXIST . YTD::TAG_WRONG));
    }

    /**
     * @return array<mixed>
     */
    public static function providerReplaceTags(): array
    {
        $domWrongWithNode = self::wrongDomMultiple();
        $replaceWithNode = $domWrongWithNode->createElement(YTD::TAG_EXIST_NAME);

        return [
            'emp-emp-empDom' => [0, YTD::TAG_EMPTY, YTD::TAG_EMPTY, self::emptyDom()],
            'ex-emp-empDom' => [0, YTD::TAG_EXIST_NAME, YTD::TAG_EMPTY, self::emptyDom()],
            'wro-emp-empDom' => [0, YTD::TAG_WRONG_NAME, YTD::TAG_EMPTY, self::emptyDom()],
            // --
            'emp-emp-wroDom' => [0, YTD::TAG_EMPTY, YTD::TAG_EMPTY, self::wrongDom()],
            'ex-emp-wroDom' => [0, YTD::TAG_EXIST_NAME, YTD::TAG_EMPTY, self::wrongDom()],
            'wro-emp-wroDom' => [0, YTD::TAG_WRONG_NAME, YTD::TAG_EMPTY, self::wrongDom()],
            // --
            'emp-ex-wroDom' => [0, YTD::TAG_EMPTY, YTD::TAG_EXIST_NAME, self::wrongDom()],
            'ex-ex-wroDom' => [0, YTD::TAG_EXIST_NAME, YTD::TAG_EXIST_NAME, self::wrongDom()],
            'wro-ex-wroDom' => [0, YTD::TAG_WRONG_NAME, YTD::TAG_EXIST_NAME, self::wrongDom()],
            // --
            'emp-wro-wroDom' => [0, YTD::TAG_EMPTY, YTD::TAG_WRONG_NAME, self::wrongDom()],
            'ex-wro-wroDom' => [0, YTD::TAG_EXIST_NAME, YTD::TAG_WRONG_NAME, self::wrongDom()],
            'wro-wro-wroDom' => [1, YTD::TAG_WRONG_NAME, YTD::TAG_WRONG_NAME, self::wrongDom()],
            // --
            'emp-emp-exDom' => [0, YTD::TAG_EMPTY, YTD::TAG_EMPTY, self::existDom()],
            'ex-emp-exDom' => [0, YTD::TAG_EXIST_NAME, YTD::TAG_EMPTY, self::existDom()],
            'wro-emp-exDom' => [0, YTD::TAG_WRONG_NAME, YTD::TAG_EMPTY, self::existDom()],
            // --
            'emp-ex-exDom' => [0, YTD::TAG_EMPTY, YTD::TAG_EXIST_NAME, self::existDom()],
            'ex-ex-exDom' => [1, YTD::TAG_EXIST_NAME, YTD::TAG_EXIST_NAME, self::existDom()],
            'wro-ex-exDom' => [0, YTD::TAG_WRONG_NAME, YTD::TAG_EXIST_NAME, self::existDom()],
            // --
            'emp-wro-exDom' => [0, YTD::TAG_EMPTY, YTD::TAG_WRONG_NAME, self::existDom()],
            'ex-wro-exDom' => [0, YTD::TAG_EXIST_NAME, YTD::TAG_WRONG_NAME, self::existDom()],
            'wro-wro-exDom' => [0, YTD::TAG_WRONG_NAME, YTD::TAG_WRONG_NAME, self::existDom()],
            // --
            'emp-emp-exDomM' => [0, YTD::TAG_EMPTY, YTD::TAG_EMPTY, self::wrongDomMultiple()],
            'ex-emp-exDomM' => [0, YTD::TAG_EXIST_NAME, YTD::TAG_EMPTY, self::wrongDomMultiple()],
            'wro-emp-exDomM' => [0, YTD::TAG_WRONG_NAME, YTD::TAG_EMPTY, self::wrongDomMultiple()],
            // --
            'emp-ex-exDomM' => [0, YTD::TAG_EMPTY, YTD::TAG_EXIST_NAME, self::wrongDomMultiple()],
            'ex-ex-exDomM' => [1, YTD::TAG_EXIST_NAME, YTD::TAG_EXIST_NAME, self::wrongDomMultiple()],
            'wro-ex-exDomM' => [0, YTD::TAG_WRONG_NAME, YTD::TAG_EXIST_NAME, self::wrongDomMultiple()],
            // --
            'emp-wro-exDomM' => [0, YTD::TAG_EMPTY, YTD::TAG_WRONG_NAME, self::wrongDomMultiple()],
            'ex-wro-exDomM' => [0, YTD::TAG_EXIST_NAME, YTD::TAG_WRONG_NAME, self::wrongDomMultiple()],
            'wro-wro-exDomM' => [3, YTD::TAG_WRONG_NAME, YTD::TAG_WRONG_NAME, self::wrongDomMultiple()],
            // --
            'wro-exNode-wroDom' => [0, YTD::TAG_WRONG_NAME, $replaceWithNode, $domWrongWithNode],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function providerGetTagFindTag(): array
    {
        return [
            'emptyTagEmptyDOM' => [0, YTD::TAG_EMPTY, YTD::TAG_EMPTY, self::emptyDom()],
            'existTagEmptyDOM' => [0, YTD::TAG_EMPTY, YTD::TAG_EXIST_NAME, self::emptyDom()],
            'emptyTagExistDOM' => [0, YTD::TAG_EMPTY, YTD::TAG_EMPTY, self::existDom()],
            'wrongTagExistDOM' => [0, YTD::TAG_EMPTY, YTD::TAG_WRONG_NAME, self::existDom()],
            'existTagExistDOM' => [1, YTD::TAG_EXIST_NAME, YTD::TAG_EXIST_NAME, self::existDom()],
            'existTagExistDOMShort' => [1, YTD::TAG_EXIST_NAME, YTD::TAG_EXIST_NAME, YTD::prepareDOMDocument(sprintf(YTD::TAG_ROOT, '', YTD::TAG_EXIST_SHORT))],
            'wrongTagMultipleDOM' => [3, YTD::TAG_WRONG_NAME, YTD::TAG_WRONG_NAME, self::wrongDomMultiple()],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function providerDeleteTag(): array
    {
        return [
            'emptyTagEmptyDOM' => [0, YTD::TAG_EMPTY, self::emptyDom(), false],
            'existTagEmptyDOM' => [0, YTD::TAG_EXIST_NAME, self::emptyDom(), false],
            'emptyTagExistDOM' => [0, YTD::TAG_EMPTY, self::existDom(), false],
            'wrongTagExistDOM' => [0, YTD::TAG_WRONG_NAME, self::existDom(), false],
            'existTagExistDOM' => [1, YTD::TAG_EXIST_NAME, self::existDom(), false],
            'wrongTagMultipleDOMFirst' => [1, YTD::TAG_WRONG_NAME, self::wrongDomMultiple(), false],
            'wrongTagMultipleDOMAll' => [3, YTD::TAG_WRONG_NAME, self::wrongDomMultiple(), true],
        ];
    }
}
