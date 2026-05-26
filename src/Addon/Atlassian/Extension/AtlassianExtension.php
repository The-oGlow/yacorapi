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

use oglow\tools\Addon\Atlassian\Macro\AtlassianAddon;
use oglow\tools\Yacorapi\Extension\AbstractExtension;

class AtlassianExtension extends AbstractExtension
{
    protected function init(): void
    {
        parent::init();
        $this->addons = new AtlassianAddon();
    }

    public static function getName(): string
    {
        return 'Atlassian Extension';
    }

    public static function getId(): int
    {
        return 2;
    }
}
