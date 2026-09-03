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

namespace oglow\tools\Addon\Atlassian\Extension;

use oglow\tools\Addon\Atlassian\Macro\AdminAddon;
use oglow\tools\Yacorapi\Extension\AbstractExtension;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\IConnectionProvider;

class AdminExtension extends AbstractExtension
{

    #[\Override]
    protected function init(): void
    {
        parent::init();
        $this->addons = new AdminAddon();
    }

    #[\Override]
    public static function getName(): string
    {
        return 'Atlassian Admin Extension';
    }

    #[\Override]
    public static function getId(): int
    {
        return ExtensionEnum::EXTENSION_ATLASSIAN_ADMIN->value;
    }
}
