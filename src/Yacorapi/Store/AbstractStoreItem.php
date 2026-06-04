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

use Ds\Map;
use Monolog\ConsoleLogger;
use Psr\Log\LoggerInterface;

abstract class AbstractStoreItem implements IStoreItem
{
    /** @var Map<mixed,mixed> */
    protected Map $storeItems;

    private static LoggerInterface $logger;

    protected function __construct()
    {
        self::$logger = new ConsoleLogger(AbstractStoreItem::class);
        self::$logger->debug("START");

        $this->storeItems = new Map();

        self::$logger->debug("END");
    }
}
