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

namespace oglow\tools\Yacorapi\Response;

use Ds\Collection;
use Ds\Map;
use Ds\Vector;
use oglow\tools\Yacorapi\Response\ResponseParameter as RP;
use oglow\tools\Yacorapi\Space\SpaceInfoEnum;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class ResponseTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): Response
    {
        return new Response();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): Response
    {
        return $this->o2t;
    }

    public function testGetRawData(): void
    {
        $expected = Map::class;
        $expectedCount = 0;

        $actual = $this->getCasto2t()->getRawData();

        self::assertInstanceOf($expected, $actual);
        self::assertCount($expectedCount, $actual);
    }

    public function testKeyExists(): void
    {
        $expected = false;

        $actual = $this->getCasto2t()->keyExists(YacorapiTestData::NOTEXIST_ID);

        self::assertEquals($expected, $actual);
    }

    public function testKeys(): void
    {
        $expected = Vector::class;
        $expectedCount = 0;

        $actual = $this->getCasto2t()->keys();

        self::assertInstanceOf($expected, $actual);
        self::assertCount($expectedCount, $actual);
    }

    public function testGetValue(): void
    {
        $expected = YacorapiTestData::KEY_ALPHA2;

        $actual = $this->getCasto2t()->getValue(YacorapiTestData::KEY_ALPHA1, YacorapiTestData::KEY_ALPHA2);

        self::assertEquals($expected, $actual);
    }

    public function testCheckStatus(): void
    {
        $expected = true;

        $actual = $this->getCasto2t()->checkStatus();

        self::assertEquals($expected, $actual);
    }

    public function testCheckData(): void
    {
        $expected = false;

        $actual = $this->getCasto2t()->checkData();

        self::assertEquals($expected, $actual);
    }

    public function testCheckDataWrite(): void
    {
        $expected = false;

        $actual = $this->getCasto2t()->checkDataWrite();

        self::assertEquals($expected, $actual);
    }

    public function testGetResults(): void
    {
        $expected = Map::class;
        $expectedCount = 0;

        $actual = $this->getCasto2t()->getResults();

        self::assertInstanceOf($expected, $actual);
        self::assertCount($expectedCount, $actual);
    }

    public function testGetResult(): void
    {
        $actual = $this->getCasto2t()->getResult(YacorapiTestData::KEY_NUM1);

        self::assertNull($actual);
    }

    public function testGetResultsCount(): void
    {
        $expectedCount = 0;

        $actual = $this->getCasto2t()->getResultsCount();

        self::assertEquals($expectedCount, $actual);
    }

    public function testHasResults(): void
    {
        $expected = false;

        $actual = $this->getCasto2t()->hasResults();

        self::assertEquals($expected, $actual);
    }

    public function testGetBody(): void
    {
        $expected = '';

        $actual = $this->getCasto2t()->getBody();

        self::assertEquals($expected, $actual);
    }

    public function testGetItemId(): void
    {
        $expected = -1;

        $actual = $this->getCasto2t()->getItemId();

        self::assertEquals($expected, $actual);
    }

    public function testGetLabels(): void
    {
        $expected = Vector::class;
        $expectedCount = 0;

        $actual = $this->getCasto2t()->getLabels();

        self::assertInstanceOf($expected, $actual);
        self::assertCount($expectedCount, $actual);
    }

    public function testLabelExists(): void
    {
        $expected = false;
        $labelName = YacorapiTestData::DATA_ALPHA1;

        $actual = $this->getCasto2t()->labelExists($labelName);

        self::assertEquals($expected, $actual);
    }

    public function testGetRestrictions(): void
    {
        $expectedCount = 0;

        $actual = $this->getCasto2t()->getRestrictions();

        self::assertIsArray($actual);
        self::assertCount($expectedCount, $actual);
    }

    /**
     * @param Collection<mixed,mixed>|int|string $expected
     * @param bool                               $expectedPrimitive
     * @param SpaceInfoEnum                      $flags
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerGetSpaceInfo')]
    public function testGetSpaceInfo(Collection|string|int $expected, bool $expectedPrimitive, SpaceInfoEnum $flags): void
    {
        $actual = $this->getCasto2t()->getSpaceInfo($flags);

        self::assertEquals($expectedPrimitive, self::isPrimitive($actual));
        if ($actual instanceof Map && $expected instanceof Map) {
            self::assertEqualsCanonicalizing($expected->keys()->toArray(), $actual->keys()->toArray());
        } else {
            self::assertEquals($expected, $actual);
        }
    }

    /**
     * @return array<mixed>
     */
    public static function providerGetSpaceInfo(): array
    {
        return [
            'spaceId' => [RP::VAL_SPACE_ID_NO, true, SpaceInfoEnum::SPACEINFO_ID],
            'spaceKey' => [RP::VAL_SPACE_KEY_NO, false, SpaceInfoEnum::SPACEINFO_KEY],
            'spaceTitle' => [RP::VAL_SPACE_TITLE_EMPTY, false, SpaceInfoEnum::SPACEINFO_TITLE],
            'spaceType' => [RP::VAL_SPACE_TYPE_EMPTY, false, SpaceInfoEnum::SPACEINFO_TYPE],
            'ALL' => [
                new Map([
                    RP::KEY_KEY => RP::VAL_SPACE_ID_NO,
                    RP::KEY_TITLE => RP::VAL_SPACE_TITLE_EMPTY,
                    RP::KEY_TYPE => RP::VAL_SPACE_TYPE_EMPTY,
                    RP::KEY_ID => RP::VAL_SPACE_KEY_NO,
                        ]),
                false,
                SpaceInfoEnum::SPACEINFO_ALL,
            ],
        ];
    }
}
