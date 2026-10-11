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

namespace oglow\tools\Yacorapi\Extension;

use Ds\Map;
use Ds\Seq;

interface IExtension
{
    public static function getName(): string;

    public static function getId(): int;

    /**
     * Returns the addons and their assigned macros.
     *
     * @return Map<mixed,Seq>
     */
    public function getAddons(): Map;

    /**
     * @return Seq
     */
    public function getMacros(): Seq;
}
