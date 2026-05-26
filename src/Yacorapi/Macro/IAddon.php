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

use Ds\Map;
use Ds\Vector;

interface IAddon
{
    /**
     * Returns the addons and their assigned macros.
     *
     * @return Map<mixed,Vector<mixed>>
     *
     * @see getAddonNames()
     * @see getMacros()
     * @see getMacrosArray()
     */
    public function getAddons(): Map;

    /**
     * Returns the names of the addons.
     *
     * @return Vector<mixed>
     *
     * @see getAddons()
     */
    public function getAddonNames(): Vector;

    /**
     * Returns the macros without any addons as vector.
     *
     * @return Vector<mixed>
     *
     * @see getAddons()
     *
     * @sse getMacrosArray()
     */
    public function getMacros(): Vector;

    /**
     * Returns the macros without any addons as array.
     *
     * @return array<mixed,mixed>
     *
     * @see getAddons()
     * @see getMacros()
     */
    public function getMacrosArray(): array;
}
