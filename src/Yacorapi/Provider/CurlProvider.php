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
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Request\RequestType;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class CurlProvider extends AbstractProvider
{
    /** @var LoggerInterface */
    private static $logger;

    /** @var null|IResponse */
    private $dryRunResponse;

    public function __construct(?IResponse $dryRunResponse = null, string $logLevel = LogLevel::INFO)
    {
        self::$logger = new ConsoleLogger(CurlProvider::class, $logLevel);
        self::$logger->debug('START');

        parent::__construct($logLevel);
        $this->dryRunResponse = $dryRunResponse;

        self::$logger->debug('END');
    }

    /**
     * @param string $execUrl
     * @param int    $reqType
     *
     * @return array<mixed,mixed>
     */
    protected function execInternal(string $execUrl, int $reqType = RequestType::REQ_TYP_GET)
    {
        self::$logger->debug('START - execUrl,reqType', [$execUrl, $reqType]);

        $curlSession = $this->prepareCurl($reqType);
        $rawData     = $this->execCurl($curlSession, $execUrl);
        self::$logger->debug('rawData elements', [empty($rawData) ? 0 : count($rawData)]);

        self::$logger->debug('END');

        return $rawData;
    }

    /**
     * @param string           $execUrl
     * @param Map<mixed,mixed> $parameters
     * @param int              $reqType
     *
     * @return array<mixed,mixed>
     */
    protected function execPostInternal(string $execUrl, Map $parameters, $reqType = RequestType::REQ_TYP_PUT)
    {
        self::$logger->debug('START - execUrl,parameters,reqType', [$execUrl, $parameters, $reqType]);

        $curlSession = $this->prepareCurlWrite($parameters, $reqType);
        $rawData     = $this->execCurl($curlSession, $execUrl);
        self::$logger->debug('rawData elements', [empty($rawData) ? 0 : count($rawData)]);

        self::$logger->debug('END');

        return $rawData;
    }

    /**
     * @param int $reqType
     *
     * @return false|resource
     */
    private function prepareCurl(int $reqType)
    {
        self::$logger->debug('START - reqType', [$reqType]);

        $newSession = curl_init();
        // Set cURL options
        curl_setopt($newSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($newSession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->prepareCertificate($newSession);
        $this->prepareAuthorisation($newSession);

        self::$logger->debug('END');

        return $newSession;
    }

    /**
     * @param Map<mixed, mixed> $parameters
     * @param int               $reqType
     *
     * @return false|resource
     */
    private function prepareCurlWrite(Map $parameters, int $reqType)
    {
        self::$logger->debug('START - parameters,reqType', [$parameters, $reqType]);

        $newSession = $this->prepareCurl($reqType);
        switch ($reqType) {
            case RequestType::REQ_TYP_POST: {
                $this->preparePostParameter($newSession, $parameters);
                break;
            }
            case RequestType::REQ_TYP_PUT:
            default: {
                $this->preparePutParameter($newSession, $parameters);
            }
        }

        self::$logger->debug('END');

        return $newSession;
    }

    /**
     * Run the query.
     *
     * @param false|resource $execSession
     * @param string         $execUrl
     * @param bool           $dryRun
     *
     * @return array<mixed,mixed>
     */
    private function execCurl($execSession, string $execUrl, bool $dryRun = false)
    {
        self::$logger->debug('START - execUrl,dryRun', [$execUrl, $dryRun]);

        /** @var array<mixed,mixed> */
        $rawData = [];
        if ($dryRun) {
            self::$logger->notice('DRYRUN is activated');
            if (!is_null($this->dryRunResponse)) {
                $rawData = $this->dryRunResponse->getResponse()->toArray();
            }
        } else {
            if (is_resource($execSession) && !empty($execUrl)) {
                curl_setopt($execSession, CURLOPT_URL, $execUrl);
                $curlResponse = curl_exec($execSession);

                // Check for errors
                if (curl_errno($execSession) !== 0) {
                    self::$logger->error('curl_error', [curl_error($execSession)]);
                }

                // Close the cURL session
                curl_close($execSession);

                // Decode the response
                if (is_string($curlResponse)) {
                    $rawData = json_decode($curlResponse, true);
                }
            }
        }
        self::$logger->debug('END');

        return $rawData;
    }

    /**
     * @param false|resource $newSession
     */
    private function prepareCertificate(&$newSession): void
    {
        self::$logger->debug('START');

        if (is_resource($newSession)) {
            if (file_exists($this->constData->c(ConstData::KEY_MY_CERT_CA))) {
                curl_setopt($newSession, CURLOPT_CAINFO, $this->constData->c(ConstData::KEY_MY_CERT_CA));
            } else {
                self::$logger->warning('CA certificate not found!', [$this->constData->c(ConstData::KEY_MY_CERT_CA)]);
                curl_setopt($newSession, CURLOPT_SSL_VERIFYPEER, false); // NOSONAR: php:S4830
            }
        }

        self::$logger->debug('END');
    }

    /**
     * @param false|resource $newSession
     */
    private function prepareAuthorisation(&$newSession): void
    {
        self::$logger->debug('START');

        if (is_resource($newSession)) {
            $token = $this->getTokenValue();
            if (!empty($token)) {
                curl_setopt(
                    $newSession,
                    CURLOPT_HTTPHEADER,
                    [
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token,
                    ]
                );
            } else {
                $auth = $this->getAuthValue();
                if (!empty($auth)) {
                    curl_setopt($newSession, CURLOPT_USERPWD, $auth);
                }
            }
        }

        self::$logger->debug('END');
    }

    /**
     * @param false|resource    $newSession
     * @param Map<mixed, mixed> $parameters
     */
    private function preparePutParameter(&$newSession, Map $parameters): void
    {
        self::$logger->debug('START - parameters', [$parameters]);

        if (is_resource($newSession)) {
            curl_setopt($newSession, CURLOPT_CUSTOMREQUEST, 'PUT');
            $parametersAsString = json_encode($parameters);
            if (is_string($parametersAsString)) {
                curl_setopt($newSession, CURLOPT_POSTFIELDS, $parametersAsString);
            }
        }

        self::$logger->debug('END');
    }

    /**
     * @param false|resource    $newSession
     * @param Map<mixed, mixed> $parameters
     */
    private function preparePostParameter(&$newSession, Map $parameters): void
    {
        self::$logger->debug('START - parameters', [$parameters]);

        if (is_resource($newSession)) {
            curl_setopt($newSession, CURLOPT_POST, true);
            $parametersAsString = json_encode($parameters);
            self::$logger->debug('parameters', [$parametersAsString]);
            if (is_string($parametersAsString)) {
                curl_setopt($newSession, CURLOPT_POSTFIELDS, $parametersAsString);
            }
        }

        self::$logger->debug('END');
    }
}
