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

use Ds\Collection;
use Monolog\ConsoleLogger;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Macro\AddonMacroData;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Provider\CurlProvider;
use oglow\tools\Yacorapi\Request\RequestTypeEnum;
use ollily\Tools\Reflection\MagicPublicFunctionTrait;
use Psr\Log\LoggerInterface;
abstract class AbstractRapiClient
{
    use MagicPublicFunctionTrait;

    private static LoggerInterface $logger;

    protected ConstData $constData;

    protected IContainer $addons;

    protected IConnectionProvider $connectionProvider;

    /**
     * RapiClient constructor.
     *
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|IContainer                 $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link IRapiClientBase::LEVEL_DEFAULT})
     */
    protected function __construct(
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ) {
        // Init Logger
        /** @psalm-suppress ArgumentTypeCoercion 
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name:AbstractRapiClient::class, level:$level);
        self::$logger->debug('START');

        // Init Dynamic Consts
        $this->constData = new ConstData(get_class($this));
        // Init Modules
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
     * @param string                  $prepareUrl
     * @param Collection<mixed,mixed> $parameters
     * @param RequestTypeEnum         $reqType
     *
     * @return IResponse
     */
    protected function execPost(string $prepareUrl, Collection $parameters, RequestTypeEnum $reqType): IResponse
    {
        self::$logger->debug('START - prepareUrl,parameters,reqType', [$prepareUrl, $parameters, $reqType]);

        $response = $this->connectionProvider->execPost($prepareUrl, $parameters, $reqType);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @param string $comment
     * @param string $userId  Additional userId (Default {@link self::APP_USER}
     *
     * @return string A cleaned comment
     *
     * @see self::APP_USER
     */
    protected function validateComment(string $comment, string $userId = ConstData::VAL_APP_USER): string
    {
        $cleanComment = '';
        if (!empty($comment)) {
            $cleanComment = substr($comment, 0, IRapiClientBase::VAL_COMMENT_MAXLEN);
            if (!str_contains($cleanComment, ConstData::VAL_APP_USER)) {
                $cleanComment = sprintf('%s (%s)', $cleanComment, $userId);
            }
        }

        return $cleanComment;
    }
}
