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

namespace oglow\tools\Yacorapi\Traits;

use Monolog\ConsoleLogger;
use oglow\tools\common\MockProvider;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\IConnectionProvider;
use Psr\Log\LoggerInterface;

class PrepPermissionTraitTestClazz
{
    use PrepPermissionTrait;

    protected ConstData $constData;

    private static LoggerInterface $logger;

    protected IConnectionProvider $connectionProvider;

    public function __construct()
    {
        self::$logger    = new ConsoleLogger(PrepPermissionTraitTestClazz::class);
        $this->constData = new ConstData(PrepPermissionTraitTestClazz::class);
        $this->connectionProvider  = new MockProvider();
    }
}
