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

namespace oglow\tools\Addon\Projectdoc\Extension;

use oglow\tools\Yacorapi\ConstData;
use PHPUnit\Framework\TestCase;

class ProjectdocExtensionTest extends TestCase
{
    public const CLAZZNAME = '\oglow\tools\Addon\Projectdoc\Extension\ProjectdocExtension';

    public function testClazzExist(): void
    {
        $clazz = static::CLAZZNAME;

        try {
            $actual = new $clazz(new ConstData());
            self::assertInstanceOf(static::CLAZZNAME, $actual);
        } catch (\Exception $e) {
            self::fail('Should not raise any exection: ' . $e->getMessage());
        }
    }
}
