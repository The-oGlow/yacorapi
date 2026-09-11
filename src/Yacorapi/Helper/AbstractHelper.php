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
use oglow\tools\common\AbstractSingleton;
use oglow\tools\Yacorapi\ConstData;
use Psr\Log\LoggerInterface;

/**
 * Abstract implementation for a helper clazz.
 * 
 * @author ollily
 */
abstract class AbstractHelper extends AbstractSingleton implements IHelper
{
    protected ConstData $constData;

    private static LoggerInterface $logger;

    /**
     * Public constructor.
     * 
     * @param string $key Unique id of this singleton
     * @param bool $withLogger TRUE=activate logging, else FALSE
     */
    public function __construct(string $key, bool $withLogger = true)
    {
        if ($withLogger) {
            self::$logger = new ConsoleLogger(AbstractHelper::class, level: static::LEVEL_DEFAULT);
        } else {
            self::$logger = new DoNothingLogger();
        }
        self::$logger->debug('START');
        parent::__construct($key, $withLogger);
        // Init Dynamic Consts
        $this->constData = new ConstData($key);
        self::$logger->debug('END');
    }
}
