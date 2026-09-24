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

/**
 * Abstract implementation for a store item.
 *
 * @author ollily
 *
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
abstract class AbstractStoreItem implements IStoreItem
{
    /** @var Map<mixed,mixed> */
    protected Map $storeItems;

    private static LoggerInterface $logger;

    /**
     * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level The minimum logging level at which this handler will be triggered
     *                                                              (Default: {@link AbstractStoreItem::LEVEL_DEFAULT})
     *
     * @phpstan-param LoggingLevel $level
     */
    protected function __construct(mixed $level = AbstractStoreItem::LEVEL_DEFAULT)
    {
        self::$logger = new ConsoleLogger(AbstractStoreItem::class, level: $level);
        self::$logger->debug("START");

        $this->storeItems = new Map();

        self::$logger->debug("END");
    }
}
