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

namespace oglow\tools\common;

use Psr\Log\LogLevel;

/**
 * Interface for a singleton.
 *
 * @author olliy
 */
interface ISingleton
{
    /** @var string Default output level */
    public const string LEVEL_DEFAULT = LogLevel::DEBUG;
}
