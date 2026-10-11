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

class RapiClientExtension extends AbstractExtension
{
    #[\Override]
    public static function getName(): string
    {
        return 'RAPI-Client Extension';
    }

    #[\Override]
    public static function getId(): int
    {
        return ExtensionEnum::EXTENSION_RAPI_CLIENT->value;
    }
}
