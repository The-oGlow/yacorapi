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
use Ds\Seq;
use oglow\tools\Yacorapi\YacorapiTestData;

class IExtensionTestDummyClazz implements IExtension
{
    #[\Override]
    public static function getName(): string
    {
        return IExtensionTestDummyClazz::class;
    }

    #[\Override]
    public static function getId(): int
    {
        return YacorapiTestData::NOTEXIST_ID;
    }

    /**
     * Returns the addons and their assigned macros.
     *
     * @return Map<mixed,Seq>
     */
    #[\Override]
    public function getAddons(): Map
    {
        return new Map();
    }

    /**
     * @return Seq
     */
    #[\Override]
    public function getMacros(): Seq
    {
        return new Seq();
    }
}
