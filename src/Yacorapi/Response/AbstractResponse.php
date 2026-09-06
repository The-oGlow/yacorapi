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

use Ds\Collection;
use Ds\Map;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\IResponse;
use ollily\Tools\String\ToStringTrait;
use Psr\Log\LoggerInterface;

abstract class AbstractResponse implements IResponse
{
    use ToStringTrait;

    private static LoggerInterface $logger;

    /** @var Collection<mixed,mixed>
     * @phpstan-var Map<mixed,mixed> */
    private Collection $rawData;

    /** @var Collection<mixed,mixed>
     * @phpstan-var Map<mixed,mixed> */
    private Collection $results;

    /**
     * Response constructor.
     *
     * @param array<mixed,mixed> $data
     */
    public function __construct(array $data = [])
    {
        self::$logger = new ConsoleLogger(AbstractResponse::class);
        self::$logger->debug('START');

        $this->prepareData($data);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getRawData(): Collection
    {
        return $this->rawData;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keyExists($key): bool
    {
        return !empty($key) && $this->rawData->hasKey($key);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keys(): Vector
    {
        return new Vector($this->rawData->keys());
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getValue(mixed $key, mixed $default = ''): mixed
    {
        $value = $default;
        if ($this->keyExists($key)) {
            $value = $this->rawData->get($key, $default);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkStatus(): bool
    {
        self::$logger->debug('START');

        $statusOk = false;
        if ($this->keyExists(ResponseParameterData::KEY_STATUS_CODE)) {
            self::$logger->debug(ResponseParameterData::ERR_MSG_COMMON, $this->getError()->toArray());
        } else {
            $statusOk = true;
        }
        self::$logger->debug('END', [$statusOk]);

        return $statusOk;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getError(): Collection
    {
        $error = new Map();
        if ($this->keyExists(ResponseParameterData::KEY_STATUS_CODE)) {
            $error->put(ResponseParameterData::KEY_STATUS_CODE, $this->getValue(ResponseParameterData::KEY_STATUS_CODE));
            $error->put(ResponseParameterData::KEY_REASON, $this->getValue(ResponseParameterData::KEY_REASON));
            $error->put(ResponseParameterData::KEY_MESSAGE, $this->getValue(ResponseParameterData::KEY_MESSAGE));
        }

        return $error;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkData(): bool
    {
        self::$logger->debug('START');

        if ($this->isResultsAvailable()) {
            $hasData = $this->checkStatus();
            if ($hasData) {
                if (!$this->keyExists(ResponseParameterData::KEY_RESULTS) || $this->getValue(ResponseParameterData::KEY_SIZE) <= 0) {
                    self::$logger->debug('Response has no results');
                    $hasData = false;
                } else {
                    self::$logger->debug('Response has results with size', [$this->keyExists(ResponseParameterData::KEY_RESULTS), $this->getValue(ResponseParameterData::KEY_SIZE)]);
                }
            }
        } else {
            self::$logger->info('Response has no results');
            $hasData = false;
        }

        self::$logger->debug('END - hasData', [$hasData]);

        return $hasData;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkDataWrite(): mixed
    {
        self::$logger->debug('START');

        if ($this->isResultsAvailable()) {
            $hasData = $this->checkStatus();
            if ($hasData) {
                if (!$this->keyExists(ResponseParameterData::KEY_KEY) || $this->getValue(ResponseParameterData::KEY_KEY) <= 0) {
                    self::$logger->info('No pageId found or is 0');
                    $hasData = false;
                } else {
                    $pageId = $this->getValue(ResponseParameterData::KEY_KEY);
                    self::$logger->notice('Write to pageId', [$pageId]);
                    $hasData = $pageId;
                }
            }
        } else {
            self::$logger->info('Results are not available');
            $hasData = false;
        }

        self::$logger->debug('END - hasData', [$hasData]);

        return $hasData;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getResults(): Collection
    {
        return $this->results;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getResult(int $idx): mixed
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
    #[\Override]
    public function isResultsAvailable(): bool
    {
        return !$this->results->isEmpty();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getBody(): string
    {
        $body = '';
        if ($this->keyExists(ResponseParameterData::KEY_BODY)) {
            $tmpBody = $this->getValue(ResponseParameterData::KEY_BODY, []);
            if (array_key_exists(ResponseParameterData::KEY_STORAGE, $tmpBody)) {
                if (array_key_exists(ResponseParameterData::KEY_VALUE, $tmpBody[ResponseParameterData::KEY_STORAGE])) {
                    $body = $tmpBody[ResponseParameterData::KEY_STORAGE][ResponseParameterData::KEY_VALUE];
                }
            }
        }

        return $body;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getRestrictions(): array
    {
        $restrictions = [];
        if ($this->keyExists(ResponseParameterData::KEY_RESTRICTIONS)) {
            $restrictions = $this->getValue(ResponseParameterData::KEY_RESTRICTIONS, []);
        }

        return $restrictions;
    }

    /**
     * @return mixed
     */
    #[\Override]
    protected function __toStringValues(): mixed
    {
        return [ResponseParameterData::KEY_RESPONSE => $this->rawData, ResponseParameterData::KEY_RESULTS => $this->results];
    }

    /**
     * @param array<mixed,mixed> $data
     */
    private function prepareData(array $data = []): void
    {
        if (array_key_exists(ResponseParameterData::KEY_RESULTS, $data)) {
            $this->results = new Map($data[ResponseParameterData::KEY_RESULTS]);
            unset($data[ResponseParameterData::KEY_RESULTS]);
        } else {
            $this->results = new Map([]);
        }
        $this->rawData = new Map($data);
    }
}
