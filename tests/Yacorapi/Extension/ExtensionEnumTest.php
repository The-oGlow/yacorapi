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

namespace oglow\tools\Yacorapi\Extension;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExtensionEnumTest extends TestCase
{
    #[DataProvider('providerIsIn')]
    public function testIsIn(bool $expected, ExtensionEnum $expectedExtension, ExtensionEnum $actualExtension): void
    {
        $actual = $expectedExtension->isIn($actualExtension);

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerIsIn(): array
    {
        return [
          'theSame'  => [true, ExtensionEnum::EXTENSION_ATLASSIAN, ExtensionEnum::EXTENSION_ATLASSIAN],
          'notTheSame' => [false, ExtensionEnum::EXTENSION_ATLASSIAN, ExtensionEnum::EXTENSION_ATLASSIAN_ADMIN],
          'isIn' => [true, ExtensionEnum::EXTENSION_ATLASSIAN, ExtensionEnum::EXTENSION_ALL],
          'notIsIn' => [false, ExtensionEnum::EXTENSION_THIRD_PARTY, ExtensionEnum::EXTENSION_MIN],
          'allExt' => [true, ExtensionEnum::EXTENSION_ALL, ExtensionEnum::EXTENSION_ALL],
        ];
    }
}
