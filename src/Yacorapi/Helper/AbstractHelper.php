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

namespace oglow\tools\Yacorapi\Helper;

use Monolog\ConsoleLogger;
use Monolog\DoNothingLogger;
use ollily\Common\AbstractSingleton;
use oglow\tools\Yacorapi\ConstData;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Abstract implementation for a helper clazz.
 *
 * @author ollily
 *
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
abstract class AbstractHelper extends AbstractSingleton implements IHelper
{
    protected ConstData $constData;

    private static LoggerInterface $logger;

    /**
     * Protected constructor.
     *
     * @param bool                   $withLogger TRUE=activate logging, else FALSE
     * @param int|LogLevel::*|string $level      The minimum logging level at which this handler will be triggered (Default: {@link self::LEVEL_DEFAULT})
     */
    protected function __construct(bool $withLogger = true, LogLevel|string|int $level = self::LEVEL_DEFAULT)
    {
        if ($withLogger) {
            /** @psalm-suppress ArgumentTypeCoercion
             * @phpstan-ignore argument.type */
            self::$logger = new ConsoleLogger(AbstractHelper::class, level: $level);
        } else {
            self::$logger = new DoNothingLogger();
        }
        self::$logger->debug('START');

        parent::__construct($withLogger, $level);
        // Init Dynamic Consts
        $this->constData = ConstData::i();

        self::$logger->debug('END');
    }
}
