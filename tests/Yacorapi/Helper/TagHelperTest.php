<?php

/*
 * Copyright 2026 postm.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace oglow\tools\Yacorapi\Helper;

use Ds\Sequence;
use Ds\Vector;
use PHPUnit\Framework\EasyGoingTestCase;
use DOMDocument;
use DOMElement;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\Attributes\DataProvider;
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
    protected static function prepareO2t(): TagHelper
    {
        return TagHelper::i();
    }
    
    /**
     * @param Sequence $expected
     * @param string $tagName
     * @param DOMDocument $domPage
     */
    #[DataProvider('providerFindTag')]
    public function testfindTag(Sequence $expected, string $tagName, DOMDocument $domPage): void
    {
        try {
        $actual = $this->getCasto2t()::findTag($tagName, $domPage);
        } catch (\Throwable $thrown) {
            self::fail($thrown->getMessage());
        }
        self::assertEqualsCanonicalizing($expected, $actual);
    }
    
    // Dataprovider
    
    /**
     * @return array<mixed,mixed>
     */
    public static function providerFindTag() : array {
        $emptyDom = new DOMDocument(encoding: 'UTF-8');
        $emptyExpected = new Vector();

        $existDom = new DOMDocument(encoding: 'UTF_8' );
        $existDom->loadHTML(YacorapiTestData::TAG_EXIST);
        $existDomElement = $existDom->createElement(YacorapiTestData::TAG_EXIST_NAME);
        $existExpected = new Vector([$existDomElement]);

        $existDomShort = new DOMDocument(encoding: 'UTF_8');
        $existDomShort->loadHTML(YacorapiTestData::TAG_EXIST_SHORT);

        return [
          'emptyTagEmptyDOM' => [$emptyExpected, YacorapiTestData::TAG_EMPTY, $emptyDom],
          'existTagEmptyDOM' => [$emptyExpected, '//'.YacorapiTestData::TAG_EXIST_NAME, $emptyDom],
          'emptyTagexistDOM' => [$emptyExpected, YacorapiTestData::TAG_EMPTY, $existDom],
          'wrongTagexistDOM' => [$emptyExpected, '//'.YacorapiTestData::TAG_WRONG_NAME, $existDom],
          'existTagexistDOM' => [$existExpected, '//'.YacorapiTestData::TAG_EXIST_NAME, $existDom],
          'existShortTagexistDOM' => [$existExpected, '//'.YacorapiTestData::TAG_EXIST_NAME, $existDomShort],
        ];
        
    }
}
