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
use Ds\Vector;

interface IExtension
{
    public const int EXTENSION_RAPI_CLIENT          = 1;

    public const int EXTENSION_ATLASSIAN            = 2;

    public const int EXTENSION_ATLASSIAN_ADMIN      = 4;

    public const int EXTENSION_ATLASSIAN_USER_MACRO = 8;

    public const int EXTENSION_THIRD_PARTY          = 16;

    public const int EXTENSION_PROJECTDOC_TOOLBOX   = 32;

    public const int EXTENSION_MIN                  = self::EXTENSION_RAPI_CLIENT + self::EXTENSION_ATLASSIAN;

    public const int EXTENSION_ALL                  = self::EXTENSION_MIN +
    self::EXTENSION_ATLASSIAN_ADMIN +
    self::EXTENSION_ATLASSIAN_USER_MACRO +
    self::EXTENSION_THIRD_PARTY +
    self::EXTENSION_PROJECTDOC_TOOLBOX;

    public static function getName(): string;

    public static function getId(): int;

    /**
     * Returns the addons and their assigned macros.
     *
     * @return Map<mixed,Vector<mixed>>
     */
    public function getAddons(): Map;

    /**
     * @return Vector<mixed>
     */
    public function getMacros(): Vector;
}
