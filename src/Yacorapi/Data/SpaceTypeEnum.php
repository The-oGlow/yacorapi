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

namespace oglow\tools\Yacorapi\Data;

enum SpaceTypeEnum: string
{
    case SPACE_TYPE_GLOBAL = 'global';
    case SPACE_TYPE_PERSONAL = 'personal';
    case SPACE_SINGLE = 'single';
    case SPACE_SIMPLE = 'simple';
    case SPACE_ALL = 'all';

    public function method(): string
    {
        return match ($this) {
            self::SPACE_SINGLE => 'getMySpaceListSingle',
            self::SPACE_SIMPLE => 'getMySpaceListSimple',
            self::SPACE_ALL => 'getMySpaceListAll',
            default => $this->value
        };
    }
}
