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

use CurlHandle;
use Ds\Map;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\ExitCodes;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Request\RequestType;
use oglow\tools\Yacorapi\Response\ResponseDryRun;
use ollily\Tools\Emergency;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-import-type LoggingLevel from AbstractProvider
 */
class CurlProvider extends AbstractProvider
{
    private static LoggerInterface $logger;

    private IResponse $dryRunResponse;

    /**
     * @param int|string     $level
     * @param null|IResponse $dryRunResponse
     *
     * @see self::LEVEL_DEFAULT
     *
     * @phpstan-param LoggingLevel $level
     */
    public function __construct(int|string $level = self::LEVEL_DEFAULT, null|IResponse $dryRunResponse = null)
    {
        self::$logger = new ConsoleLogger(name: CurlProvider::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($level);
        if (is_null($dryRunResponse)) {
            $this->dryRunResponse = new ResponseDryRun();
        } else {
            $this->dryRunResponse = $dryRunResponse;
        }

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function execInternal(string $execUrl, int $reqType = RequestType::REQ_TYP_GET): array
    {
        self::$logger->debug('START - execUrl,reqType', [$execUrl, $reqType]);

        $curlSession = $this->prepareCurl($reqType);
        $rawData     = $this->execCurl($curlSession, $execUrl);
        self::$logger->debug('rawData elements', [empty($rawData) ? 0 : count($rawData)]);

        self::$logger->debug('END');

        return $rawData;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function execPostInternal(string $execUrl, Map $parameters, int $reqType = RequestType::REQ_TYP_PUT): array
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
     * @return CurlHandle|false
     */
    private function prepareCurl(int $reqType)
    {
        self::$logger->debug('START - reqType', [$reqType]);

        $newSession = curl_init();

        if ($newSession instanceof CurlHandle) {
            // Set cURL options
            curl_setopt($newSession, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($newSession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $this->prepareCertificate($newSession);
            $this->prepareAuthorisation($newSession);
        } else {
            Emergency::breakSystem(ExitCodes::ERR_CODE_CURL_INIT, "Cannot create curl session");
        }

        self::$logger->debug('END');

        return $newSession;
    }

    /**
     * @param Map<mixed, mixed> $parameters
     * @param int               $reqType
     *
     * @return CurlHandle|false
     */
    private function prepareCurlWrite(Map $parameters, int $reqType)
    {
        self::$logger->debug('START - parameters,reqType', [$parameters, $reqType]);

        $execSession = $this->prepareCurl($reqType);
        switch ($reqType) {
            case RequestType::REQ_TYP_POST: {
                $this->preparePostParameter($execSession, $parameters);
                break;
            }
            case RequestType::REQ_TYP_PUT:
            default: {
                $this->preparePutParameter($execSession, $parameters);
            }
        }

        self::$logger->debug('END');

        return $execSession;
    }

    /**
     * Run the query.
     *
     * @param CurlHandle|false $execSession
     * @param string           $execUrl
     * @param bool             $dryRun
     *
     * @return array<mixed,mixed>
     */
    private function execCurl($execSession, string $execUrl, bool $dryRun = false): array
    {
        self::$logger->debug('START - execUrl,dryRun', [$execUrl, $dryRun]);

        /** @var array<mixed,mixed> */
        $rawData = [];
        if ($dryRun) {
            self::$logger->notice('DRYRUN is activated');
            $rawData = $this->dryRunResponse->getResponse()->toArray();
        } else {
            if ($execSession instanceof CurlHandle && !empty($execUrl)) {
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
                    $isJson = json_validate($curlResponse);
                    if ($isJson) {
                        $rawData = json_decode($curlResponse, true);
                    } else {
                        Emergency::breakSystem(ExitCodes::ERR_CODE_RESPONSE_INVALID_OR_NULL, 'Response is invalid or null');
                    }
                } else {
                    Emergency::breakSystem(ExitCodes::ERR_CODE_RESPONSE_INVALID_OR_NULL, 'Response is invalid or null');
                }
            }
        }

        if (is_null($rawData)) {
            $rawData = [];
            Emergency::breakSystem(ExitCodes::ERR_CODE_RESPONSE_INVALID_OR_NULL, 'Response is null');
        }
        self::$logger->debug('END');

        return $rawData;
    }

    /**
     * @param CurlHandle|false $execSession
     */
    private function prepareCertificate(&$execSession): void
    {
        self::$logger->debug('START');

        if ($execSession instanceof CurlHandle) {
            if (file_exists($this->constData->c(ConstData::KEY_MY_CERT_CA))) {
                curl_setopt($execSession, CURLOPT_CAINFO, $this->constData->c(ConstData::KEY_MY_CERT_CA));
            } else {
                self::$logger->warning('CA certificate not found!', [$this->constData->c(ConstData::KEY_MY_CERT_CA)]);
                curl_setopt($execSession, CURLOPT_SSL_VERIFYPEER, false); // NOSONAR: php:S4830
            }
        }

        self::$logger->debug('END');
    }

    /**
     * @param CurlHandle|false $execSession
     */
    private function prepareAuthorisation(&$execSession): void
    {
        self::$logger->debug('START');

        if ($execSession instanceof CurlHandle) {
            $token = $this->getTokenValue();
            if (!empty($token)) {
                curl_setopt(
                    $execSession,
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
                    curl_setopt($execSession, CURLOPT_USERPWD, $auth);
                }
            }
        }

        self::$logger->debug('END');
    }

    /**
     * @param CurlHandle|false  $execSession
     * @param Map<mixed, mixed> $parameters
     */
    private function preparePutParameter(&$execSession, Map $parameters): void
    {
        self::$logger->debug('START - parameters', [$parameters]);

        if ($execSession instanceof CurlHandle) {
            curl_setopt($execSession, CURLOPT_CUSTOMREQUEST, 'PUT');
            $parametersAsString = json_encode($parameters);
            if (is_string($parametersAsString)) {
                curl_setopt($execSession, CURLOPT_POSTFIELDS, $parametersAsString);
            }
        }

        self::$logger->debug('END');
    }

    /**
     * @param CurlHandle|false  $execSession
     * @param Map<mixed, mixed> $parameters
     */
    private function preparePostParameter(&$execSession, Map $parameters): void
    {
        self::$logger->debug('START - parameters', [$parameters]);

        if ($execSession instanceof CurlHandle) {
            curl_setopt($execSession, CURLOPT_POST, true);
            $parametersAsString = json_encode($parameters->toArray());
            self::$logger->debug('parameters', [$parametersAsString]);
            if (is_string($parametersAsString)) {
                curl_setopt($execSession, CURLOPT_POSTFIELDS, $parametersAsString);
            }
        }

        self::$logger->debug('END');
    }
}
