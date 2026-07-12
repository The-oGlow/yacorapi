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

namespace oglow\tools\Addon\ThirdParty\Extension;

use oglow\tools\Addon\ThirdParty\Macro\ThirdPartyAddon;
use oglow\tools\Yacorapi\Extension\AbstractExtension;

class ThirdPartyExtension extends AbstractExtension
{
    #[\Override]
    protected function init(): void
    {
        parent::init();
        $this->addons = new ThirdPartyAddon();
    }

    #[\Override]
    public static function getName(): string
    {
        return 'Third Party Extension';
    }

    #[\Override]
    public static function getId(): int
    {
        return 16;
    }
}
