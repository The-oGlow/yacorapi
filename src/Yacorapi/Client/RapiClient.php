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

use Ds\Set;
use Monolog\ConsoleLogger;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\Extension\ExtensionTrait;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use Psr\Log\LoggerInterface;

class RapiClient extends AbstractRapiClient implements IRapiClient // NOSONAR: php:S1448
{
    use ExtensionTrait;
    use ClientReadTrait;
    use ClientWriteTrait;
    use ClientPermissionTrait;
    use ClientStatisticTrait;
    use ClientBatchTrait;

    private static LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function newClient(
        ?ExtensionEnum $modeExtension = IRapiClientBase::EXTENSION_DEFAULT,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ): IRapiClient {
        /** @psalm-suppress PossiblyInvalidArgument
         * @phpstan-ignore argument.type */
        return new RapiClient($modeExtension, $connectionProvider, $addons, $level);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function taskitemMethods(): Set
    {
        return self::existingMethodNames();
    }

    /**
     * RapiClient constructor.
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
        // Init Logger
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name: RapiClient::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($connectionProvider, $addons, $level);

        // Init Extensions
        if (is_null($modeExtension)) {
            $modeExtension = ExtensionEnum::EXTENSION_ALL;
        }
        $this->loadExtensions($modeExtension);

        self::$logger->debug('END');
    }
}
