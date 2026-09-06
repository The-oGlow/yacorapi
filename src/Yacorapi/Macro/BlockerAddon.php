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

/**
 * Replace in the method {@link getMacros()} with your addons and macros which blocks your tasks.
 */
class BlockerAddon extends AbstractAddon
{
    public const AddonTypeEnum ADDON_TYPE = AddonTypeEnum::ADDON_BLOCKER;
}
