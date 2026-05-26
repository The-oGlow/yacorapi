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

namespace oglow\tools\Yacorapi\Provider;

use Ds\Map;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Request\RequestType;
use oglow\tools\Yacorapi\Response\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

abstract class AbstractProvider implements IConnectionProvider
{
    /** @var LoggerInterface */
    private static $logger;

    /** @var ConstData */
    protected $constData;

    public function __construct(string $logLevel = LogLevel::INFO)
    {
        // Init Dynamic Consts
        $this->constData = new ConstData(AbstractProvider::class);
        self::$logger = new ConsoleLogger(AbstractProvider::class, $logLevel);
    }

    /**
     * @param string $execUrl
     * @param int    $reqType
     *
     * @return IResponse
     */
    public function exec(string $execUrl, int $reqType = RequestType::REQ_TYP_GET): IResponse
    {
        self::$logger->debug('START - execUrl,reqType', [$execUrl, $reqType]);

        $rawData = $this->execInternal($execUrl, $reqType);
        $response     = $this->prepareResponse($rawData);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @param string            $execUrl
     * @param Map<mixed, mixed> $parameters
     * @param int               $reqType
     *
     * @return IResponse
     */
    public function execPost(string $execUrl, Map $parameters, int $reqType = RequestType::REQ_TYP_PUT): IResponse
    {
        self::$logger->debug('START - execUrl,parameters,reqType', [$execUrl, $parameters, $reqType]);

        $rawData = $this->execPostInternal($execUrl, $parameters, $reqType);
        $response = $this->prepareResponse($rawData);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @param null|array<mixed,mixed> $data
     *
     * @return IResponse
     */
    public function prepareResponse($data): IResponse
    {
        self::$logger->debug('START');

        if (!is_null($data)) {
            self::$logger->debug('Prepare new response');
            $response = new Response($data);
        } else {
            self::$logger->info('Returning empty default response');
            $response = new Response();
        }

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @param string $execUrl
     * @param int    $reqType
     *
     * @return array<mixed,mixed>
     */
    abstract protected function execInternal(string $execUrl, int $reqType);

    /**
     * @param string           $execUrl
     * @param Map<mixed,mixed> $parameters
     * @param int              $reqType
     *
     * @return array<mixed,mixed>
     */
    abstract protected function execPostInternal(string $execUrl, Map $parameters, int $reqType);

    /**
     * @return string
     */
    protected function getTokenValue(): string
    {
        self::$logger->debug('START');

        $tokenValue = getenv($this->constData->c(ConstData::KEY_AUTH_TOKEN_NAME));
        if (!is_string($tokenValue) || empty($tokenValue)) {
            self::$logger->warning('Token is NOT set!', [$this->constData->c(ConstData::KEY_AUTH_TOKEN_NAME)]);
            $tokenValue = '';
        }

        self::$logger->debug('END');

        return $tokenValue;
    }

    /**
     * @return string
     */
    protected function getAuthValue(): string
    {
        self::$logger->error(self::MSG_NOT_IMPLEMENTED);

        return '#username#:#password#';
    }
}
