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

namespace oglow\tools\Yacorapi\Client;

use Monolog\ConsoleLogger;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\Extension\ExtensionTrait;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use Psr\Log\LoggerInterface;

class RapiClient extends RapiClientBatch implements IRapiClient // NOSONAR: php:S1448
{
    use ExtensionTrait;

    private static LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param null|ExtensionEnum              $modeExtension      (Default: {@link IRapiClientBase::EXTENSION_DEFAULT})
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|IContainer                 $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link IRapiClientBase::LEVEL_DEFAULT})
     */
    protected function __construct(
        ?ExtensionEnum $modeExtension = IRapiClientBase::EXTENSION_DEFAULT,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ) {
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name: RapiClient::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($modeExtension, $connectionProvider, $addons, $level);

        self::$logger->debug('END');
    }
}
