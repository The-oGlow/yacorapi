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
use Ds\Sequence;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Response\ResponseParameter as RP;
use oglow\tools\Yacorapi\Space\SpaceInfoEnum as SIEnum;
use ollily\Tools\String\ToStringTrait;
use Psr\Log\LoggerInterface;

/**
 * Abstract implementation for the response structure.
 *
 * @author ollily
 *
 * @SuppressWarnings("PMD.ExcessiveClassComplexity")
 */
abstract class AbstractResponse implements IResponse
{
    use ToStringTrait;

    private static LoggerInterface $logger;

    /** @var Map<mixed,mixed> */
    private Collection $rawData;

    /** @var Map<mixed,mixed> */
    private Collection $results;

    /** @var Vector<mixed> */
    private Sequence $labels;

    private string $body;

    /**
     * Response constructor.
     *
     * @param array<mixed> $data
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
        $statusOk = false;
        if ($this->keyExists(RP::KEY_STATUS_CODE)) {
            self::$logger->debug(RP::ERR_MSG_COMMON, $this->getError()->toArray());
        } else {
            $statusOk = true;
        }
        self::$logger->debug('statusOk', [$statusOk]);

        return $statusOk;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getError(): Collection
    {
        /** @var Map<mixed,mixed> */
        $error = new Map();
        if ($this->keyExists(RP::KEY_STATUS_CODE)) {
            $error->put(RP::KEY_STATUS_CODE, $this->getValue(RP::KEY_STATUS_CODE));
            $error->put(RP::KEY_REASON, $this->getValue(RP::KEY_REASON));
            $error->put(RP::KEY_MESSAGE, $this->getValue(RP::KEY_MESSAGE));
        }

        return $error;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkData(): bool
    {
        if ($this->hasResults()) {
            $hasData = $this->checkStatus();
            if ($hasData) {
                if (!$this->keyExists(RP::KEY_RESULTS) || $this->getValue(RP::KEY_SIZE) <= 0) {
                    self::$logger->debug('Response size <=', [0]);
                    $hasData = false;
                } else {
                    self::$logger->debug('Response size =', [
                        $this->keyExists(RP::KEY_RESULTS), $this->getValue(RP::KEY_SIZE)]);
                }
            }
        } else {
            self::$logger->info('Response has no results');
            $hasData = false;
        }

        self::$logger->debug('hasData', [$hasData]);

        return $hasData;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function checkDataWrite(): int|bool
    {
        if ($this->hasResults()) {
            $hasData = $this->checkStatus();
            if ($hasData) {
                if (RP::VAL_PAGE_ID_NO == $this->getItemId()) {
                    self::$logger->warning('No itemId set');
                    $hasData = false;
                } else {
                    $itemId = $this->getItemId();
                    self::$logger->debug('Will write to itemId', [$itemId]);
                    $hasData = $itemId;
                }
            }
        } else {
            self::$logger->info('Response has no results');
            $hasData = false;
        }

        self::$logger->debug('hasData', [$hasData]);

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
        if ($this->hasResults()) {
            $result = $this->results->toArray()[$idx];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getResultsCount(): int
    {
        return $this->results->count();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function hasResults(): bool
    {
        return !$this->results->isEmpty();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getItemId(): int
    {
        return intval($this->getValue(RP::KEY_ID, RP::VAL_PAGE_ID_NO));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getSpaceInfo(SIEnum $flags = SIEnum::SPACEINFO_ALL): mixed
    {
        $info = '';
        if ($flags == SIEnum::SPACEINFO_ALL) {
            $info = new Map();
            $info->putAll($this->prepareSpaceInfo(SIEnum::SPACEINFO_ID));
            $info->putAll($this->prepareSpaceInfo(SIEnum::SPACEINFO_KEY));
            $info->putAll($this->prepareSpaceInfo(SIEnum::SPACEINFO_TITLE));
            $info->putAll($this->prepareSpaceInfo(SIEnum::SPACEINFO_TYPE));
        } else {
            $tmpInfo = $this->prepareSpaceInfo($flags);
            if ($tmpInfo instanceof Map) {
                $info = $tmpInfo->first()->value;
            }
        }

        return $info;
    }

    /**
     * @param SIEnum $flags
     *
     * @return Collection<mixed,mixed>
     */
    protected function prepareSpaceInfo(SIEnum $flags): Collection
    {
        $space = $this->getValue(RP::KEY_SPACE);
        switch (true) {
            case (SIEnum::SPACEINFO_ID->value & $flags->value) == SIEnum::SPACEINFO_ID->value:
                $tmpValue = intval(empty($space) ? RP::VAL_SPACE_ID_NO : $space[RP::KEY_ID]);
                $tmpKey = RP::KEY_ID;
                break;
            case (SIEnum::SPACEINFO_KEY->value & $flags->value) == SIEnum::SPACEINFO_KEY->value:
                $tmpValue = empty($space) ? RP::VAL_SPACE_KEY_NO : $space[RP::KEY_KEY];
                $tmpKey = RP::KEY_KEY;
                break;
            case (SIEnum::SPACEINFO_TITLE->value & $flags->value) == SIEnum::SPACEINFO_TITLE->value:
                $tmpValue = empty($space) ? RP::VAL_SPACE_TITLE_EMPTY : $space[RP::KEY_TITLE];
                $tmpKey = RP::KEY_TITLE;
                break;
            case (SIEnum::SPACEINFO_TYPE->value & $flags->value) == SIEnum::SPACEINFO_TYPE->value:
                $tmpValue = empty($space) ? RP::VAL_SPACE_TYPE_EMPTY : $space[RP::KEY_TYPE];
                $tmpKey = RP::KEY_TYPE;
                break;
            default:
                $tmpKey = '';
                $tmpValue = '';
                break;
        }

        return  new Map([$tmpKey => $tmpValue]);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getLabels(): Sequence
    {
        return $this->labels;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function labelExists(string $labelName): bool
    {
        return $this->labels->contains($labelName);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getRestrictions(): array
    {
        return $this->getValue(RP::KEY_RESTRICTIONS, []);
    }

    /**
     * @return mixed
     */
    #[\Override]
    protected function __toStringValues(): mixed
    {
        return [RP::KEY_RESPONSE => $this->rawData, RP::KEY_RESULTS => $this->results];
    }

    /**
     * @param array<mixed> $rawData
     */
    private function prepareData(array $rawData = []): void
    {
        // Separate results
        if (array_key_exists(RP::KEY_RESULTS, $rawData)) {
            $this->results = new Map($rawData[RP::KEY_RESULTS]);
            unset($rawData[RP::KEY_RESULTS]);
        } else {
            $this->results = new Map([]);
        }

        // Separate labels
        $this->labels = new Vector();
        if (array_key_exists(RP::KEY_METADATA, $rawData)) {
            if (array_key_exists(RP::KEY_LABELS, $rawData[RP::KEY_METADATA])) {
                if (array_key_exists(RP::KEY_RESULTS, $rawData[RP::KEY_METADATA][RP::KEY_LABELS])) {
                    $this->labels = new Vector(array_column($rawData[RP::KEY_METADATA][RP::KEY_LABELS][RP::KEY_RESULTS], RP::KEY_NAME));
                    unset($rawData[RP::KEY_METADATA][RP::KEY_LABELS][RP::KEY_RESULTS]);
                }
            }
        }

        // Separate body
        $this->body = '';

        if (array_key_exists(RP::KEY_BODY, $rawData)) {
            if (array_key_exists(RP::KEY_STORAGE, $rawData[RP::KEY_BODY])) {
                if (array_key_exists(RP::KEY_VALUE, $rawData[RP::KEY_BODY][RP::KEY_STORAGE])) {
                    $this->body = $rawData[RP::KEY_BODY][RP::KEY_STORAGE][RP::KEY_VALUE];
                    unset($rawData[RP::KEY_BODY][RP::KEY_STORAGE][RP::KEY_VALUE]);
                }
            }
        }

        $this->rawData = new Map($rawData);
    }
}
