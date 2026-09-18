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
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\YacorapiTestData as YTD;
use ollily\Tools\Reflection\UnavailableFieldsTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\EasyGoingTestCase;

/**
 * @author olliy
 */
class AbstractSingletonTest extends EasyGoingTestCase
{
    use UnavailableFieldsTrait;

    /**
     * @inheritDoc
     */
    #[\Override]
    protected static function prepareO2t(): AbstractSingletonTestDummyClazz
    {
        return AbstractSingletonTestDummyClazz::i();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): AbstractSingletonTestDummyClazz
    {
        return $this->o2t;
    }

    /**
     * @param mixed $expected
     * @param bool  $withLogger
     */
    #[DataProvider('providerConstruct')]
    public function testConstruct(mixed $expected, bool $withLogger): void
    {
        // Kill the existing instance in the singleton
        $this->setFieldByReflection(AbstractSingleton::class, 'instance', AbstractSingletonTestDummyClazz::i(), []);

        $actual = AbstractSingletonTestDummyClazz::i($withLogger);
        $actualLogger = $this->getFieldByReflection(AbstractSingleton::class, 'logger', $actual);

        self::assertInstanceOf(AbstractSingletonTestDummyClazz::class, $actual);
        self::assertEquals($withLogger, ConsoleLogger::class == get_class($actualLogger));
        self::assertEquals($expected, $actual::getKey());
    }

    public function testGetKey(): void
    {
        $expected = [AbstractSingletonTestDummyClazz::class, YTD::KEY_ALPHA1];

        $actual = $this->getCasto2t()::getKey();

        self::assertContains($actual, $expected);
    }

    public function testParseBoolCollection(): void
    {
        $expected = YTD::DATA_EMPTY;

        $actual = $this->getCasto2t()->publicParseBoolCollection(new Map(), YTD::KEY_ALPHA1);

        self::assertEquals($expected, $actual);
    }

    public function testPrepareShortOpts(): void
    {
        $expected = YTD::DATA_EMPTY;

        $actual = $this->getCasto2t()->publicPrepareShortOpts();

        self::assertEquals($expected, $actual);
    }

    public function testPrepareLongOpts(): void
    {
        $expected = YTD::ARRAY_EMPTY;

        $actual = $this->getCasto2t()->publicPrepareLongOpts();

        self::assertEquals($expected, $actual);
    }

    public function testPrepareSettings(): void
    {
        $this->getCasto2t()->publicPrepareSettings(new Map());

        self::assertTrue(true);
    }

    public function testValidateSettings(): void
    {
        $expected = true;

        $actual = $this->getCasto2t()->publicValidateSettings(new Map());

        self::assertEquals($expected, $actual);
    }

    // Data provider

    /**
     * @return array<mixed>
     */
    public static function providerConstruct(): array
    {
        return [
            'WithLogger' => [AbstractSingletonTestDummyClazz::class,  YTD::DATA_BOOL_T],
            'NoLogger' => [AbstractSingletonTestDummyClazz::class,  YTD::DATA_BOOL_F],
        ];
    }
}
