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

use Ds\Map;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\EasyGoingTestCase;

class AbstractHelperTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): AbstractHelperTestDummyClazz
    {
        return AbstractHelperTestDummyClazz::i();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): AbstractHelperTestDummyClazz
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
        $actual = AbstractHelperTestDummyClazz::i($withLogger);

        self::assertInstanceOf(AbstractHelperTestDummyClazz::class, $actual);
        self::assertEquals($expected, $actual::getKey());
    }

    /**
     * @return array<mixed>
     */
    public static function providerConstruct(): array
    {
        return [
            'keyTrue' => [AbstractHelperTestDummyClazz::class, YacorapiTestData::DATA_BOOL_T],
            'keyFalse' => [AbstractHelperTestDummyClazz::class,  YacorapiTestData::DATA_BOOL_F],
        ];
    }

    public function testValidateSettings(): void
    {
        $expected = true;

        $actual = $this->getCasto2t()->publicValidateSettings(new Map());

        self::assertEquals($expected, $actual);
    }
}
