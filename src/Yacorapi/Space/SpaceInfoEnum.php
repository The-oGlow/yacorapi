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

namespace oglow\tools\Yacorapi\Space;

/**
 * @author ollily
 */
enum SpaceInfoEnum: int
{
    /**
     * All parts of the spaceinfo returned as a collection.
     */
    case SPACEINFO_ALL = 15;

    /**
     * The unique numeric id of the space.
     */
    case SPACEINFO_ID = 1;

    /**
     * The unique key of the space.
     */
    case SPACEINFO_KEY = 2;

    /**
     * The name or title of the space.
     */
    case SPACEINFO_TITLE = 4;

    /**
     * The type of the space. @see SpaceTypeEnum.
     */
    case SPACEINFO_TYPE = 8;
}
