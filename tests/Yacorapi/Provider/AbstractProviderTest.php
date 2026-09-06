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

namespace oglow\tools\Yacorapi\Provider;

use Ds\Map;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Request\RequestTypeEnum;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class AbstractProviderTest extends EasyGoingTestCase
{
    #[\Override]
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return AbstractProviderTestDummyClazz
     */
    #[\Override]
    protected static function prepareO2t(): AbstractProviderTestDummyClazz
    {
        return new AbstractProviderTestDummyClazz();
    }

    /**
     * @return AbstractProviderTestDummyClazz
     */
    #[\Override]
    protected function getCasto2t(): AbstractProviderTestDummyClazz
    {
        return $this->o2t;
    }

    /**
     * @param IResponse          $response
     * @param array<mixed,mixed> $expectedData
     */
    protected function verifyResponse(IResponse $response, array $expectedData = []): void
    {
        self::assertNotEmpty($response);
        self::assertInstanceOf(IResponse::class, $response);
        self::assertInstanceOf(Map::class, $response->getRawData());
        self::assertInstanceOf(Map::class, $response->getResults());
        self::assertEquals($expectedData, $response->getResults()->toArray());
    }

    public function testPrepareResponseDataEmpty(): void
    {
        $result = $this->getCasto2t()->prepareResponse([]);
        $this->verifyResponse($result);
    }

    public function testPrepareResponseDataWrong(): void
    {
        static::expectException(\TypeError::class);
        $result = $this->getCasto2t()->prepareResponse('WrongType'); // @phpstan-ignore argument.type
        $this->verifyResponse($result);
    }

    public function testPrepareResponseDataNull(): void
    {
        $result = $this->getCasto2t()->prepareResponse(null);
        $this->verifyResponse($result);
    }

    public function testExecInternal(): void
    {
        $expected = YacorapiTestData::ARRAY_EMPTY;

        $actual = $this->getCasto2t()->publicExecInternal(YacorapiTestData::DATA_EMPTY, RequestTypeEnum::GET);

        self::assertEquals($expected, $actual);
    }

    public function testExecPostInternal(): void
    {
        $expected = YacorapiTestData::ARRAY_EMPTY;

        $actual = $this->getCasto2t()->publicExecPostInternal(YacorapiTestData::DATA_EMPTY, new Map(), RequestTypeEnum::POST);

        self::assertEquals($expected, $actual);
    }

    public function testGetTokenValue(): void
    {
        $expected1 = 0;
        $expected2 = 16;

        $actual = $this->getCasto2t()->publicGetTokenValue();

        self::assertThat(
            strlen($actual),
            self::logicalOr(
                self::equalTo($expected1),
                self::greaterThanOrEqual($expected2)
            )
        );
    }

    public function testGetAuthValue(): void
    {
        $expected = 0;

        $actual = $this->getCasto2t()->publicGetAuthValue();

        self::assertGreaterThanOrEqual($expected, strlen($actual));
    }
}
