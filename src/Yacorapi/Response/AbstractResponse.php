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

namespace oglow\tools\Yacorapi\Response;

use Ds\Map;
use Ds\Set;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\IResponse;
use ollily\Tools\String\ToStringTrait;
use Psr\Log\LoggerInterface;

abstract class AbstractResponse implements IResponse
{
    use ToStringTrait;

    private static LoggerInterface $logger;

    /** @var Map<mixed,mixed> */
    private Map $response;

    /** @var Map<mixed,mixed> */
    private Map $results;

    /**
     * Response constructor.
     *
     * @param null|array<mixed,mixed> $data
     */
    public function __construct(?array $data = null)
    {
        self::$logger = new ConsoleLogger(AbstractResponse::class);
        self::$logger->debug('START');

        $this->prepareData($data);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    public function getResponse(): Map
    {
        return $this->response;
    }

    /**
     * @inheritDoc
     */
    public function keyExists($key): bool
    {
        return !empty($key) && $this->response->hasKey($key);
    }

    /**
     * @inheritDoc
     */
    public function keys(): Set
    {
        return $this->response->keys();
    }

    /**
     * @inheritDoc
     */
    public function getValue($key, $default = '')
    {
        $value = $default;
        if ($this->keyExists($key)) {
            $value = $this->response->get($key, $default);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function checkStatus(): bool
    {
        self::$logger->debug('START');

        $statusOk = false;
        if ($this->keyExists(self::KEY_STATUS_CODE)) {
            self::$logger->error(
                self::MSG_ERROR,
                [$this->getValue(self::KEY_STATUS_CODE), $this->getValue(self::KEY_REASON), $this->getValue(self::KEY_MESSAGE)]
            );
        } else {
            $statusOk = true;
        }
        self::$logger->debug('END', [$statusOk]);

        return $statusOk;
    }

    /**
     * @inheritDoc
     */
    public function checkData(): bool
    {
        self::$logger->debug('START');

        if ($this->isResultsAvailable()) {
            $hasData = $this->checkStatus();
            if ($hasData) {
                if (!$this->keyExists(IResponse::KEY_RESULTS) || $this->getValue(IResponse::KEY_SIZE) <= 0) {
                    self::$logger->info('Response has no results!');
                    $hasData = false;
                } else {
                    self::$logger->info('Response has results with size', [$this->keyExists(IResponse::KEY_RESULTS), $this->getValue(IResponse::KEY_SIZE)]);
                }
            }
        } else {
            self::$logger->info('Results are not available!');
            $hasData = false;
        }

        self::$logger->debug('END - hasData', [$hasData]);

        return $hasData;
    }

    /**
     * @inheritDoc
     */
    public function checkDataWrite()
    {
        self::$logger->debug('START');

        if ($this->isResultsAvailable()) {
            $hasData = $this->checkStatus();
            if ($hasData) {
                if (!$this->keyExists(IResponse::KEY_KEY) || $this->getValue(IResponse::KEY_KEY) <= 0) {
                    self::$logger->info('No pageId found or is 0!');
                    $hasData = false;
                } else {
                    $pageId = $this->getValue(IResponse::KEY_KEY);
                    self::$logger->notice('Write to pageId', [$pageId]);
                    $hasData = $pageId;
                }
            }
        } else {
            self::$logger->info('Results are not available!');
            $hasData = false;
        }

        self::$logger->debug('END - hasData', [$hasData]);

        return $hasData;
    }

    /**
     * @inheritDoc
     */
    public function getResults(): Map
    {
        return $this->results;
    }

    /**
     * @inheritDoc
     */
    public function getResult(int $idx)
    {
        $result = null;
        if ($this->isResultsAvailable()) {
            $result = $this->results->toArray()[$idx];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isResultsAvailable(): bool
    {
        return !$this->results->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function getBody(): string
    {
        $body = '';
        if ($this->keyExists(self::KEY_BODY)) {
            $tmpBody = $this->getValue(self::KEY_BODY, []);
            if (array_key_exists(self::KEY_STORAGE, $tmpBody)) {
                if (array_key_exists(self::KEY_VALUE, $tmpBody[self::KEY_STORAGE])) {
                    $body = $tmpBody[self::KEY_STORAGE][self::KEY_VALUE];
                }
            }
        }

        return $body;
    }

    /**
     * @inheritDoc
     */
    public function getRestrictions(): array
    {
        $restrictions = [];
        if ($this->keyExists(self::KEY_RESTRICTIONS)) {
            $restrictions = $this->getValue(self::KEY_RESTRICTIONS, []);
        }

        return $restrictions;
    }

    /**
     * @return array<mixed,mixed>
     *
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    protected function __toStringValues(): array
    {
        return [self::KEY_RESPONSE => $this->response, self::KEY_RESULTS => $this->results];
    }

    /**
     * @param null|array<mixed,mixed> $data
     */
    private function prepareData(?array $data = null): void
    {
        if (is_null($data)) {
            $data = [];
        }
        if (array_key_exists(self::KEY_RESULTS, $data)) {
            $this->results = new Map($data[self::KEY_RESULTS]);
            unset($data[self::KEY_RESULTS]);
        } else {
            $this->results = new Map([]);
        }
        $this->response = new Map($data);
    }
}
