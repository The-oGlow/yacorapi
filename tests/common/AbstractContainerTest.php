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

namespace oglow\tools\common;

use oglow\tools\Yacorapi\YacorapiTestData as YTD;
use PHPUnit\Framework\EasyGoingTestCase;

/**
 * @author olliy
 */
class AbstractContainerTest extends EasyGoingTestCase
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected static function prepareO2t(): AbstractContainerTestDummyClazz
    {
        return new AbstractContainerTestDummyClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): AbstractContainerTestDummyClazz
    {
        return $this->o2t;
    }

    public function testGetKeys(): void
    {
        $expected = [AbstractContainerTestDummyClazz::TEST_MODE_2, AbstractContainerTestDummyClazz::TEST_MODE_1];
        $expectedCount = 2;

        $actual = $this->getCasto2t()->getKeys();

        self::assertIsArray($actual);
        self::assertEqualsCanonicalizing($expected, $actual);
        self::assertCount($expectedCount, $actual);
    }

    public function testGetModes(): void
    {
        $expected = [AbstractContainerTestDummyClazz::TEST_MODE_2, AbstractContainerTestDummyClazz::TEST_MODE_1];
        $expectedCount = 2;

        $actual = $this->getCasto2t()->getModes();

        self::assertIsArray($actual);
        self::assertEqualsCanonicalizing($expected, $actual);
        self::assertCount($expectedCount, $actual);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerGetDataByMode')]
    public function testGetDataByMode(int $expectedCount, mixed $mode): void
    {
        $actual = $this->getCasto2t()->getDataByMode($mode);

        self::assertIsArray($actual);
        self::assertCount($expectedCount, $actual);
    }

    public function testToStringValues(): void
    {
        $expectedCount = 2;

        $actual = $this->getCasto2t()->publicToStringValues();

        self::assertIsArray($actual);
        self::assertCount($expectedCount, $actual);
    }

    // Data provider

    /**
     * @return array<mixed>
     */
    public static function providerGetDataByMode(): array
    {
        return [
            'notExists' => [0, YTD::NOTEXIST_NAME],
            'empty' => [0, YTD::KEY_EMPTY],
            'mode1' => [2, AbstractContainerTestDummyClazz::TEST_MODE_1],
            'mode2' => [0, AbstractContainerTestDummyClazz::TEST_MODE_2],
        ];
    }
}
