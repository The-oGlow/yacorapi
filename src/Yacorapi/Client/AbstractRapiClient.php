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

use Ds\Map;
use Ds\Set;
use Monolog\ConsoleLogger;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\AddonMacroData;
use oglow\tools\Yacorapi\Extension\IExtension;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Provider\CurlProvider;
use oglow\tools\Yacorapi\Request\RequestTypeEnum;
use oglow\tools\Yacorapi\Traits\ExtensionTrait;
use ollily\Tools\Reflection\MagicPublicFunctionTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class AbstractRapiClient implements IRapiClient
{
    use ExtensionTrait;
    use RapiExtensionTrait;
    use MagicPublicFunctionTrait;

    private static LoggerInterface $logger;

    protected ConstData $constData;

    protected IConnectionProvider $connectionProvider;

    /**
     * Create new RapiClient.
     *
     * @param null|int                 $modeExtension
     * @param null|IConnectionProvider $connectionProvider
     * @param null|IContainer          $addons
     * @param int|LogLevel|string      $level              (Default: {@link IRapiClient::LEVEL_DEFAULT})
     *
     * @return IRapiClient
     *
     * @see IRapiClient::LEVEL_DEFAULT
     */
    abstract public static function newClient(
        ?int $modeExtension = null,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClient::LEVEL_DEFAULT
    ): IRapiClient;

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
     * @param null|int                        $modeExtension
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|IContainer                 $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link IRapiClient::LEVEL_DEFAULT})
     *
     * @see IRapiClient::LEVEL_DEFAULT
     */
    protected function __construct(
        ?int $modeExtension = null,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClient::LEVEL_DEFAULT
    ) {
        // Init Logger
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name:AbstractRapiClient::class, level:$level);
        self::$logger->debug('START');

        // Init Dynamic Consts
        $this->constData = new ConstData(get_class($this));
        // Init Modules
        if (is_null($modeExtension)) {
            $modeExtension = IExtension::EXTENSION_ALL;
        }
        if (empty($addons)) {
            $this->addons = new AddonMacroData();
        } else {
            $this->addons = $addons;
        }
        if (empty($connectionProvider)) {
            /** @psalm-suppress ArgumentTypeCoercion
             * @phpstan-ignore argument.type */
            $this->connectionProvider = new CurlProvider($level);
        } else {
            $this->connectionProvider = $connectionProvider;
        }
        // Init Extensions
        $this->loadExtensions($modeExtension);

        self::$logger->debug('END');
    }

    /**
     * @param string          $prepareUrl
     * @param RequestTypeEnum $reqType
     *
     * @return IResponse
     */
    protected function exec(string $prepareUrl, RequestTypeEnum $reqType = RequestTypeEnum::GET): IResponse
    {
        self::$logger->debug('START', [$prepareUrl, $reqType]);

        $response = $this->connectionProvider->exec($prepareUrl, $reqType);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @param string           $prepareUrl
     * @param Map<mixed,mixed> $parameters
     * @param RequestTypeEnum  $reqType
     *
     * @return IResponse
     */
    protected function execPost(string $prepareUrl, Map $parameters, RequestTypeEnum $reqType): IResponse
    {
        self::$logger->debug('START - prepareUrl,parameters,reqType', [$prepareUrl, $parameters, $reqType]);

        $response = $this->connectionProvider->execPost($prepareUrl, $parameters, $reqType);

        self::$logger->debug('END');

        return $response;
    }
}
