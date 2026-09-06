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

use Ds\Map;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\EasyGoingTestCase;

class AbstractSingletonTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): AbstractSingletonTestDummyClazz
    {
        return new AbstractSingletonTestDummyClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): AbstractSingletonTestDummyClazz
    {
        return $this->o2t;
    }

    public function testGetKey(): void
    {
        $expected = AbstractSingletonTestDummyClazz::class;

        $actual = $this->getCasto2t()->getKey();

        self::assertEquals($expected, $actual);
    }

    /**
     * @param mixed  $expected
     * @param string $key
     * @param bool   $withLogger
     */
    #[DataProvider('providerConstruct')]
    public function testConstruct(mixed $expected, string $key, bool $withLogger): void
    {
        $actual = new AbstractSingletonTestDummyClazz($key, $withLogger);

        self::assertInstanceOf(AbstractSingletonTestDummyClazz::class, $actual);
        self::assertEquals($expected, $actual->getKey());
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerConstruct(): array
    {
        return [
            'emptyTrue' => [AbstractSingletonTestDummyClazz::class, YacorapiTestData::DATA_EMPTY, YacorapiTestData::DATA_BOOL_T],
            'emptyFalse' => [AbstractSingletonTestDummyClazz::class, YacorapiTestData::DATA_EMPTY, YacorapiTestData::DATA_BOOL_F],
            'keyTrue' => [YacorapiTestData::KEY_ALPHA1, YacorapiTestData::KEY_ALPHA1, YacorapiTestData::DATA_BOOL_T],
            'keyFalse' => [YacorapiTestData::KEY_ALPHA1, YacorapiTestData::KEY_ALPHA1, YacorapiTestData::DATA_BOOL_F],
        ];
    }

    public function testPrepareShortOpts(): void
    {
        $expected = YacorapiTestData::DATA_EMPTY;

        $actual = $this->getCasto2t()->publicPrepareShortOpts();

        self::assertEquals($expected, $actual);
    }

    public function testPrepareLongOpts(): void
    {
        $expected = YacorapiTestData::ARRAY_EMPTY;

        $actual = $this->getCasto2t()->publicPrepareLongOpts();

        self::assertEquals($expected, $actual);
    }

    public function testParseBoolCollection(): void
    {
        $expected = YacorapiTestData::DATA_EMPTY;

        $actual = $this->getCasto2t()->publicParseBoolCollection(new Map(), YacorapiTestData::KEY_ALPHA1);

        self::assertEquals($expected, $actual);
    }

    public function testValidateSettings(): void
    {
        $expected = true;

        $actual = $this->getCasto2t()->publicValidateSettings(new Map());

        self::assertEquals($expected, $actual);
    }
}
