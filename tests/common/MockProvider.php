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

use Ds\Collection;
use Ds\Map;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Request\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Provider\AbstractProvider;
use oglow\tools\Yacorapi\Request\RequestTypeEnum;
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
        self::$logger = new ConsoleLogger(name: MockProvider::class, level: $level);
        parent::__construct($level);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function execInternal(string $execUrl, RequestTypeEnum $reqType): array
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
    protected function execPostInternal(string $execUrl, Collection $parameters, RequestTypeEnum $reqType): array
    {
        self::$logger->debug('START - execUrl,parameters,reqType', [$execUrl, $parameters, $reqType]);

        $response = $this->evaluateParameterRequest($execUrl, $parameters, $reqType);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @param string          $execUrl
     * @param RequestTypeEnum $reqType
     *
     * @return array<mixed,mixed>
     */
    protected function evaluateRequest(string $execUrl, RequestTypeEnum $reqType): array
    {
        $response = [];

        switch ($reqType) {
            case RequestTypeEnum::GET:
                if ($this->evalReadPagesByTitle($execUrl, $reqType, $response)) {
                    break;
                }
                if ($this->evalReadPageByPageId($execUrl, $reqType, $response)) {
                    break;
                }
                if ($this->evalScanPages($execUrl, $reqType, $response)) {
                    break;
                }
                if ($this->evalSearchPagesWithFilter($execUrl, $reqType, $response)) {
                    break;
                }
                if ($this->evalListSpaces($execUrl, $reqType, $response)) {
                    break;
                }
                if ($this->evalSpaceHomepage($execUrl, $reqType, $response)) {
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
     * @param RequestTypeEnum    $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalReadPageByPageId(string $execUrl, RequestTypeEnum $reqType, array &$response): bool
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
            self::$logger->debug('Not a \'readPageByPageId\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param RequestTypeEnum    $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalReadPagesByTitle(string $execUrl, RequestTypeEnum $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s?', ConstData::C_RAPI_CONTENT);
        $searchParameter = YacorapiTestData::C_SEARCHPAGETITLE_01;

        if (str_contains($execUrl, $searchUrl) && str_contains($execUrl, $searchParameter)) {
            self::$logger->notice('A \'readPagesByTitle\'', [$execUrl, $reqType]);

            $response = array_merge($response, YacorapiTestData::RESP_CONTENTFILTER_RESULT());
            $done = true;
        } else {
            self::$logger->debug('Not a \'readPagesByTitle\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param RequestTypeEnum    $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalSearchPagesWithFilter(string $execUrl, RequestTypeEnum $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s?cql=', ConstData::C_RAPI_SEARCH);
        $searchParameter = sprintf('siteSearch~%s', urlencode('"' . YacorapiTestData::C_FILTERTERM_01));

        if (str_contains($execUrl, $searchUrl) && str_contains($execUrl, $searchParameter)) {
            self::$logger->notice('A \'searchPagesWithFilter\'', [$execUrl, $reqType]);

            $response = array_merge($response, YacorapiTestData::RESP_SEARCH_RESULT());
            $done = true;
        } else {
            self::$logger->debug('Not a \'searchPagesWithFilter\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param RequestTypeEnum    $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalScanPages(string $execUrl, RequestTypeEnum $reqType, array &$response): bool
    {
        $done = false;
        $searchUrl = sprintf('%s?', ConstData::C_RAPI_SCAN);
        $searchParameter = sprintf('%s=%s', RequestParameterData::PROP_SPACE_KEY, YacorapiTestData::C_SPACE_EXIST_KEY);

        if (str_contains($execUrl, $searchUrl) && str_contains($execUrl, $searchParameter)) {
            self::$logger->notice('A \'scanPages\'', [$execUrl, $reqType]);

            $response = array_merge($response, YacorapiTestData::RESP_SCAN_RESULT());
            $done = true;
        } else {
            self::$logger->debug('Not a \'scanPages\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param RequestTypeEnum    $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    protected function evalListSpaces(string $execUrl, RequestTypeEnum $reqType, array &$response): bool
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
            self::$logger->debug('Not a \'listSpaces\'', [$execUrl, $reqType]);
        }

        return $done;
    }

    /**
     * @param string                  $execUrl
     * @param Collection<mixed,mixed> $parameters
     * @param RequestTypeEnum         $reqType
     *
     * @return array<mixed,mixed>
     */
    protected function evaluateParameterRequest(string $execUrl, Collection $parameters, RequestTypeEnum $reqType): array
    {
        $response = [];

        switch ($reqType) {
            case RequestTypeEnum::POST:
                if ($this->evalCreatePage($execUrl, $parameters, $reqType, $response)) {
                    break;
                }
                self::$logger->notice('No mock result found', [$execUrl, $reqType, $parameters]);
                break;
            case RequestTypeEnum::PUT:
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
     * @param string                  $execUrl
     * @param Collection<mixed,mixed> $parameters
     * @param RequestTypeEnum         $reqType
     * @param array<mixed,mixed>      $response
     *
     * @return bool
     */
    protected function evalCreatePage(string $execUrl, Collection $parameters, RequestTypeEnum $reqType, array &$response): bool
    {
        $done = false;
        /** @var Map<mixed,mixed> */
        $mapParameters = $parameters;

        $searchUrl = sprintf('%s/', ConstData::C_RAPI_CONTENT);
        $expectedKeys = [RequestParameterData::PROP_TYPE, RequestParameterData::PROP_TITLE,
            RequestParameterData::PROP_STATUS, RequestParameterData::PROP_BODY, RequestParameterData::PROP_SPACE,
        ];
        $notExpectedKeys = [RequestParameterData::PROP_ID];

        if (str_contains($execUrl, $searchUrl) && $this->verifyKeys($mapParameters, $expectedKeys) && $this->notVerifyKeys($mapParameters, $notExpectedKeys)) {
            self::$logger->notice('A \'createPage\'', [$execUrl, $reqType, $mapParameters]);

            $response = array_merge($response, YacorapiTestData::RESP_HEAD_SEARCHPAGEID_01());
            $response = array_merge($response, [IResponse::KEY_TITLE => $mapParameters->get(RequestParameterData::PROP_TITLE)]);
            $response = array_merge($response, YacorapiTestData::prepareResponseBody('', $mapParameters));
            $response = array_merge($response, YacorapiTestData::prepareResponseSpace('', $mapParameters));
            $response = array_merge($response, YacorapiTestData::prepareResponseAncestor('', $mapParameters));

            $done = true;
        } else {
            self::$logger->debug('Not a \'createPage\'', [$execUrl, $reqType, $parameters]);
        }

        return $done;
    }

    /**
     * @param string                  $execUrl
     * @param Collection<mixed,mixed> $parameters
     * @param RequestTypeEnum         $reqType
     * @param array<mixed,mixed>      $response
     *
     * @return bool
     */
    protected function evalUpdatePage(string $execUrl, Collection $parameters, RequestTypeEnum $reqType, array &$response): bool
    {
        $done = false;
        /** @var Map<mixed,mixed> */
        $mapParameters = $parameters;

        $searchUrl = sprintf('%s/', ConstData::C_RAPI_CONTENT);
        $expectedKeys = [RequestParameterData::PROP_TYPE, RequestParameterData::PROP_TITLE,
            RequestParameterData::PROP_BODY, RequestParameterData::PROP_ID,
        ];

        if (str_contains($execUrl, $searchUrl) && $this->verifyKeys($mapParameters, $expectedKeys)) {
            self::$logger->notice('A \'updatePage\'', [$execUrl, $reqType, $mapParameters]);

            $response = array_merge($response, YacorapiTestData::RESP_HEAD_SEARCHPAGEID_01());
            $response = array_merge($response, [IResponse::KEY_TITLE => $mapParameters->get(RequestParameterData::PROP_TITLE)]);
            $response = array_merge($response, YacorapiTestData::prepareResponseBody('', $mapParameters));
            $done = true;
        } else {
            self::$logger->debug('Not a \'updatePage\'', [$execUrl, $reqType, $mapParameters]);
        }

        return $done;
    }

    /**
     * @param string             $execUrl
     * @param RequestTypeEnum    $reqType
     * @param array<mixed,mixed> $response
     *
     * @return bool
     */
    public function evalSpaceHomepage(string $execUrl, RequestTypeEnum $reqType, array &$response): bool
    {
        $done = false;

        $searchUrl = sprintf('%s/', ConstData::C_RAPI_SPACE);
        $searchParameter = YacorapiTestData::C_SPACE_EXIST_KEY;

        if (str_contains($execUrl, $searchUrl) && str_contains($execUrl, $searchParameter)) {
            self::$logger->notice('A \'spaceHomepage\'', [$execUrl, $reqType, $searchParameter]);
            $response = array_merge($response, [IResponse::KEY_HOMEPAGE => [IResponse::KEY_ID => YacorapiTestData::C_SPACE_EXIST_ID]]);
        } else {
            self::$logger->debug('Not a \'spaceHomepage\'', [$execUrl, $reqType, $searchParameter]);
        }

        return $done;
    }

    /**
     * @param Collection<mixed,mixed> $parameters
     * @param array<mixed,mixed>      $expectedKeys
     *
     * @return bool
     */
    public function verifyKeys(Collection $parameters, array $expectedKeys): bool
    {
        $verify = false;

        /** @var Map<mixed,mixed> */
        $mapParameters = $parameters;

        if (!$mapParameters->isEmpty()) {
            foreach ($expectedKeys as $expectedKey) {
                $verify = $mapParameters->hasKey($expectedKey);
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
     * @param Collection<mixed,mixed> $parameters
     * @param array<mixed,mixed>      $notExpectedKeys
     *
     * @return bool
     */
    public function notVerifyKeys(Collection $parameters, array $notExpectedKeys): bool
    {
        $verify = true;

        /** @var Map<mixed,mixed> */
        $mapParameters = $parameters;

        if (!$mapParameters->isEmpty()) {
            foreach ($notExpectedKeys as $notExpectedKeysxpectedKey) {
                $verify = !$mapParameters->hasKey($notExpectedKeysxpectedKey);
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
