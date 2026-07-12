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

use oglow\tools\Yacorapi\Traits\PrepReadTrait;
use oglow\tools\Yacorapi\Traits\PrepSpaceTrait;
use oglow\tools\Yacorapi\Traits\PrepWriteTrait;

class RapiClientExtension extends AbstractExtension
{
    use PrepReadTrait;
    use PrepSpaceTrait;
    use PrepWriteTrait;

    #[\Override]
    public static function getName(): string
    {
        return 'RAPI-Client Extension';
    }

    #[\Override]
    public static function getId(): int
    {
        return 1;
    }
}
