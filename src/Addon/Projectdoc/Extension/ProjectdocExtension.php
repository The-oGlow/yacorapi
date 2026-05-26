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

use oglow\tools\Addon\Projectdoc\Macro\ProjectdocAddon;
use oglow\tools\Addon\Projectdoc\Traits\ProjectdocTrait;
use oglow\tools\Yacorapi\Extension\AbstractExtension;

class ProjectdocExtension extends AbstractExtension
{
    use ProjectdocTrait;

    protected function init(): void
    {
        parent::init();
        $this->addons = new ProjectdocAddon();
    }

    public static function getName(): string
    {
        return 'Projectdoc Toolbox Extension';
    }

    public static function getId(): int
    {
        return 32;
    }
}
