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
use Ds\Seq;

interface IAddon
{
    /**
     * Returns the addons and their assigned macros.
     *
     * @return Map<mixed,Seq>
     *
     * @see getAddonNames()
     * @see getMacros()
     * @see getMacrosArray()
     */
    public function getAddons(): Map;

    /**
     * Returns the names of the addons.
     *
     * @return Seq
     *
     * @see getAddons()
     */
    public function getAddonNames(): Seq;

    /**
     * Returns the macros without any addons as vector.
     *
     * @return Seq
     *
     * @see getAddons()
     *
     * @sse getMacrosArray()
     */
    public function getMacros(): Seq;

    /**
     * Returns the macros without any addons as array.
     *
     * @return array<mixed>
     *
     * @see getAddons()
     * @see getMacros()
     */
    public function getMacrosArray(): array;
}
