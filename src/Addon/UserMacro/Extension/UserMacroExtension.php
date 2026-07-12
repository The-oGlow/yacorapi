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

namespace oglow\tools\Addon\UserMacro\Extension;

use oglow\tools\Addon\UserMacro\Macro\UserMacroAddon;
use oglow\tools\Yacorapi\Extension\AbstractExtension;

class UserMacroExtension extends AbstractExtension
{
    #[\Override]
    protected function init(): void
    {
        parent::init();
        $this->addons = new UserMacroAddon();
    }

    #[\Override]
    public static function getName(): string
    {
        return 'Atlassian User Macro Extension';
    }

    #[\Override]
    public static function getId(): int
    {
        return 8;
    }
}
