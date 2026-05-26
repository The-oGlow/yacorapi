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

use Ds\Map;
use Ds\Vector;
use oglow\tools\Yacorapi\YacorapiTestData;

class ExtensionTestDummyClazz implements IExtension
{
    public static function getName(): string
    {
        return ExtensionTestDummyClazz::class;
    }

    public static function getId(): int
    {
        return YacorapiTestData::NOTEXIST_ID;
    }

    /**
     * Returns the addons and their assigned macros.
     *
     * @return Map<mixed,Vector<mixed>>
     */
    public function getAddons(): Map
    {
        return new Map();
    }

    /**
     * @return Vector<mixed>
     */
    public function getMacros(): Vector
    {
        return new Vector();
    }
}
