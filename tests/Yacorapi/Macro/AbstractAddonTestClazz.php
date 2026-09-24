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

namespace oglow\tools\Yacorapi\Macro;

use Ds\Vector;
use oglow\tools\Yacorapi\YacorapiTestData;

class AbstractAddonTestClazz extends AbstractAddon
{
    #[\Override]
    protected function init(): void
    {
        parent::init();
        $this->addonsMacros->putAll(
            [
                YacorapiTestData::ADDON_1 => new Vector(YacorapiTestData::ADDON_1_ORDER),
                YacorapiTestData::ADDON_2 => new Vector(YacorapiTestData::ADDON_2_ORDER),
            ]
        );
    }
}
