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

use PHPUnit\Framework\EasyGoingTestCase;

class AbstractContainerTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): AbstractContainerTestClazz
    {
        return new AbstractContainerTestClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): AbstractContainerTestClazz
    {
        return $this->o2t;
    }

    public function testGetKeys(): void
    {
        $expected = 2;

        $actual = $this->getCasto2t()->getKeys();

        self::assertIsArray($actual);
        self::assertCount($expected, $actual);
    }

    public function testGetModes(): void
    {
        $expected = 1;

        $actual = $this->getCasto2t()->getModes();

        self::assertIsArray($actual);
        self::assertCount($expected, $actual);
    }

    public function testToStringValues(): void
    {
        $expected = 2;

        $actual = $this->getCasto2t()->publicToStringValues();

        self::assertIsArray($actual);
        self::assertCount($expected, $actual);
    }
}
