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

namespace oglow\tools\Addon\UserMacro\Macro;

use Ds\Vector;
use oglow\tools\Yacorapi\Macro\AbstractAddon;

/**
 * Class UserMacro.
 *
 * Replace inside the method {@link getMacros()} the addon 'user-macro' with your list of user macros.
 */
class UserMacroAddon extends AbstractAddon
{
    protected function init(): void
    {
        parent::init();
        $this->addonsMacros->putAll(
            [
                'Confluence User Macros' => new Vector(
                    [
                        'um-css-style',
                        'um-iframe',
                        'um-rule-blue',
                    ]
                )
            ]
        );
    }
}
