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

namespace oglow\tools\Yacorapi\Store;

/**
 * Staging options for the output pathToFile.
 * 
 * @author ollily
 */
enum FileStoreStageEnum
{
    /**
     * Storing (Default).
     */
    case BASE;

    /**
     * Storing explicit as original.
     */
    case ORIGINAL;

    /**
     * Storing explicit as modified.
     */
    case MODIFIED;

    public function isDefault(): bool
    {
        return self::BASE === $this;
    }
}
