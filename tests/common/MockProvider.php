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

namespace oglow\tools\common;

use Ds\Map;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Provider\AbstractProvider;
use oglow\tools\Yacorapi\Request\RequestType;
use oglow\tools\Yacorapi\YacorapiTestData;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-import-type LoggingLevel from AbstractProvider
 */
class MockProvider extends AbstractProvider
{
    private static LoggerInterface $logger;

    /**
     * @param int|string $level
     *
     * @see self::LEVEL_DEFAULT
     *
     * @phpstan-param LoggingLevel $level
     */
    public function __construct(int|string $level = self::LEVEL_DEFAULT)
    {
        // Init Dynamic Consts
        self::$logger = new ConsoleLogger(name:MockProvider::class, level: $level);
        parent::__construct($level);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function execInternal(string $execUrl, int $reqType): array
    {
        self::$logger->debug('START - execUrl,reqType', [$execUrl, $reqType]);

        $response = $this->evaluateRequest($execUrl, $reqType);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function execPostInternal(string $execUrl, Map $parameters, int $reqType): array
    {
        self::$logger->debug('START - execUrl,parameters,reqType', [$execUrl, $parameters, $reqType]);

        $response = $this->evaluateParameterRequest($execUrl, $parameters, $reqType);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @param string $execUrl
     * @param int    $reqType
     *
     * @return array<mixed,mixed>
     */
    protected function evaluateRequest(string $execUrl, int $reqType): array
    {
        $response = [];

        switch ($reqType) {
            case RequestType::REQ_TYP_GET:
                if ($this->evalReadPagesWithFilter($execUrl, $reqType, $response)) {
                    break;
                }
                if ($this->evalReadPageByPageId($execUrl, $reqType, $response)) {
                    break;
                }
                if ($this->evalScanPagesWithFilter($execUrl, $reqType, $response)) {
                    break;
                }
                if ($this->evalSearchPagesWithFilter($execUrl, $reqType, $response)) {
                    break;
                }
                if ($this->evalListSpaces($execUrl, $reqType, $response)) {
                    break;
                }
                self::$logger->notice('No mock result found', [$execUrl, $reqType]);
                // no break
            default: {
                self::$logger->notice('No mock for this request type', [$execUrl, $reqType]);
            }
        }

        return $response;
    }

    /**
     * @param string             $execUrl
     * @param int                $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalReadPageByPageId(string $execUrl, int $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s/%s', ConstData::C_RAPI_CONTENT, YacorapiTestData::C_SEARCHPAGEID_01);

        if (str_contains($execUrl, $searchUrl)) {
            self::$logger->notice('A \'readPageByPageId\'', [$execUrl, $reqType]);

            $response = array_merge($response, YacorapiTestData::RESP_HEAD_SEARCHPAGEID_01());
            $response = array_merge($response, YacorapiTestData::RESP_BODY());
            $response = array_merge($response, YacorapiTestData::RESP_RESTRICTION);
            $done = true;
        } else {
            self::$logger->notice('Not a \'readPageByPageId\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param int                $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalReadPagesWithFilter(string $execUrl, int $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s?', ConstData::C_RAPI_CONTENT);
        $searchParameter = YacorapiTestData::C_FILTERTERM_01;

        if (str_contains($execUrl, $searchUrl) && str_contains($execUrl, $searchParameter)) {
            self::$logger->notice('A \'readPagesWithFilter\'', [$execUrl, $reqType]);

            $response = array_merge($response, YacorapiTestData::RESP_CONTENTFILTER_RESULT());
            $done = true;
        } else {
            self::$logger->notice('Not a \'readPagesWithFilter\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param int                $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalSearchPagesWithFilter(string $execUrl, int $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s?cql=', ConstData::C_RAPI_SEARCH);
        $searchParameter = sprintf('siteSearch~%s', urlencode('"' . YacorapiTestData::C_FILTERTERM_01));

        if (str_contains($execUrl, $searchUrl) && str_contains($execUrl, $searchParameter)) {
            self::$logger->notice('A \'searchPagesWithFilter\'', [$execUrl, $reqType]);

            $response = array_merge($response, YacorapiTestData::RESP_SEARCH_RESULT());
            $done = true;
        } else {
            self::$logger->notice('Not a \'searchPagesWithFilter\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param int                $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalScanPagesWithFilter(string $execUrl, int $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s?', ConstData::C_RAPI_SCAN);
        $searchParameter = sprintf('%s=%s', RequestParameterData::PROP_SPACE_KEY, YacorapiTestData::C_SPACE_EXIST_KEY);

        if (str_contains($execUrl, $searchUrl) && str_contains($execUrl, $searchParameter)) {
            self::$logger->notice('A \'scanPagesWithFilter\'', [$execUrl, $reqType]);

            $response = array_merge($response, YacorapiTestData::RESP_SCAN_RESULT());
            $done = true;
        } else {
            self::$logger->notice('Not a \'scanPagesWithFilter\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param int                $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalListSpaces(string $execUrl, int $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s?', ConstData::C_RAPI_SPACE);

        if (str_contains($execUrl, $searchUrl)) {
            self::$logger->notice('A \'listSpaces\'', [$execUrl, $reqType]);

            $response = array_merge($response, [IResponse::KEY_TOTAL_SIZE => 1]);
            $response = array_merge(
                $response,
                [IResponse::KEY_RESULTS => [
                    [
                        IResponse::KEY_ID => YacorapiTestData::C_SPACE_EXIST_ID,
                        IResponse::KEY_KEY => YacorapiTestData::C_SPACE_EXIST_KEY,
                        IResponse::KEY_NAME => YacorapiTestData::C_SPACE_EXIST_NAME,
                        IResponse::KEY_DESCRIPTION => [
                            IResponse::KEY_PLAIN => [
                                IResponse::KEY_VALUE => YacorapiTestData::C_SPACE_EXIST_DESCRIPTION]],
                        IResponse::KEY_STATUS => YacorapiTestData::C_SPACE_EXIST_STATUS,
                        IResponse::KEY_TYPE => 0,
                    ],
                ]]
            );
            $done = true;
        } else {
            self::$logger->notice('Not a \'listSpaces\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string           $execUrl
     * @param Map<mixed,mixed> $parameters
     * @param int              $reqType
     *
     * @return array<mixed,mixed>
     */
    protected function evaluateParameterRequest(string $execUrl, Map $parameters, int $reqType): array
    {
        $response = [];

        switch ($reqType) {
            case RequestType::REQ_TYP_POST:
                if ($this->evalCreatePage($execUrl, $parameters, $reqType, $response)) {
                    break;
                }
                self::$logger->notice('No mock result found', [$execUrl, $reqType, $parameters]);
                break;
            case RequestType::REQ_TYP_PUT:
                if ($this->evalUpdatePage($execUrl, $parameters, $reqType, $response)) {
                    break;
                }
                self::$logger->notice('No mock result found', [$execUrl, $reqType, $parameters]);
                break;
            default: {
                self::$logger->notice('No mock for this request type', [$execUrl, $reqType, $parameters]);
            }
        }

        return $response;
    }

    /**
     * @param string             $execUrl
     * @param Map<mixed,mixed>   $parameters
     * @param int                $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalCreatePage(string $execUrl, Map $parameters, int $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s/', ConstData::C_RAPI_CONTENT);

        $expectedKeys = [RequestParameterData::PROP_TYPE, RequestParameterData::PROP_TITLE,
            RequestParameterData::PROP_STATUS, RequestParameterData::PROP_BODY, RequestParameterData::PROP_SPACE,
        ];
        $notExpectedKeys = [RequestParameterData::PROP_ID];

        if (str_contains($execUrl, $searchUrl) && $this->verifyKeys($parameters, $expectedKeys) && $this->notVerifyKeys($parameters, $notExpectedKeys)) {
            self::$logger->notice('A \'createPage\'', [$execUrl, $reqType, $parameters]);

            $response = array_merge($response, YacorapiTestData::RESP_HEAD_SEARCHPAGEID_01());
            $response = array_merge($response, [                IResponse::KEY_TITLE => $parameters->get(RequestParameterData::PROP_TITLE)]);
            $response = array_merge($response, YacorapiTestData::prepareResponseBody('', $parameters));
            $response = array_merge($response, YacorapiTestData::prepareResponseSpace('', $parameters));
            $response = array_merge($response, YacorapiTestData::prepareResponseAncestor('', $parameters));

            $done = true;
        } else {
            self::$logger->notice('Not a \'createPage\'', [$execUrl, $reqType, $parameters]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param Map<mixed,mixed>   $parameters
     * @param int                $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalUpdatePage(string $execUrl, Map $parameters, int $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s/', ConstData::C_RAPI_CONTENT);
        $expectedKeys = [RequestParameterData::PROP_TYPE, RequestParameterData::PROP_TITLE,
             RequestParameterData::PROP_BODY, RequestParameterData::PROP_ID,
        ];

        if (str_contains($execUrl, $searchUrl) && $this->verifyKeys($parameters, $expectedKeys)) {
            self::$logger->notice('A \'updatePage\'', [$execUrl, $reqType, $parameters]);

            $response = array_merge($response, YacorapiTestData::RESP_HEAD_SEARCHPAGEID_01());
            $response = array_merge($response, [                IResponse::KEY_TITLE => $parameters->get(RequestParameterData::PROP_TITLE)]);
            $response = array_merge($response, YacorapiTestData::prepareResponseBody('', $parameters));
            $done = true;
        } else {
            self::$logger->notice('Not a \'updatePage\'', [$execUrl, $reqType, $parameters]);
        }

        return $done;
    }

    /**
     * @param Map<mixed,mixed>   $parameters
     * @param array<mixed,mixed> $expectedKeys
     *
     * @return bool
     */
    public function verifyKeys(Map $parameters, array $expectedKeys): bool
    {
        $verify = false;

        if (!$parameters->isEmpty()) {
            foreach ($expectedKeys as $expectedKey) {
                $verify = $parameters->hasKey($expectedKey);
                if (!$verify) {
                    self::$logger->notice('Key missing', [$expectedKey]);
                    break;
                }
            }
        } else {
            self::$logger->notice('Parameters is empty');
        }

        return $verify;
    }

    /**
     * @param Map<mixed,mixed>   $parameters
     * @param array<mixed,mixed> $notExpectedKeys
     *
     * @return bool
     */
    public function notVerifyKeys(Map $parameters, array $notExpectedKeys): bool
    {
        $verify = true;

        if (!$parameters->isEmpty()) {
            foreach ($notExpectedKeys as $notExpectedKeysxpectedKey) {
                $verify = !$parameters->hasKey($notExpectedKeysxpectedKey);
                if (!$verify) {
                    self::$logger->notice('Key exists', [$notExpectedKeysxpectedKey]);
                    break;
                }
            }
        } else {
            self::$logger->notice('Parameters is empty');
        }

        return $verify;
    }
}
