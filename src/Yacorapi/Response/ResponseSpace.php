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

use BadFunctionCallException;
use Ds\Map;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\IResponse;
use Psr\Log\LoggerInterface;

/**
 * Response structure for handling space data.
 *
 * @author ollily
 */
class ResponseSpace extends AbstractResponse
{
    protected const string SPACE_ARCH_FLAG1 = '[archived]';

    protected const string SPACE_ARCH_FLAG2 = '[archive]';

    private static LoggerInterface $logger;

    /** @var array<mixed> */
    private array $spaces;

    public function __construct(IResponse $response)
    {
        self::$logger = new ConsoleLogger(ResponseSpace::class);
        self::$logger->debug('START');

        $data = $response->getRawData();
        $data->put(ResponseParameter::KEY_RESULTS, $response->getResults());

        parent::__construct($data->toArray());

        $this->spaces = $this->prepareSpaceArray($response->getResults()->toArray());

        self::$logger->debug('END');
    }

    /**
     * @return array<mixed>
     */
    public function getSpaces(): array
    {
        return $this->spaces;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keyExists($key): bool
    {
        return !empty($key) && array_key_exists($key, $this->spaces);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function keys(): Vector
    {
        // @var Vector<mixed>
        return new Vector(array_keys($this->spaces));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getValue(mixed $key, mixed $default = ''): mixed
    {
        $value = $default;
        if ($this->keyExists($key)) {
            /** @psalm-suppress MixedArrayOffset */
            $value = $this->spaces[$key];
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getResults(): Map
    {
        // @var Map<mixed,mixed>
        return new Map($this->getSpaces());
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getResult(int $idx): mixed
    {
        throw new BadFunctionCallException('Try instead ResponseSpace->getValue()');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function hasResults(): bool
    {
        return !empty($this->spaces);
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    protected function isArchived(string $text): bool
    {
        $archived = false;

        if (false !== stripos($text, self::SPACE_ARCH_FLAG1) || (false !== stripos($text, self::SPACE_ARCH_FLAG2))) {
            $archived = true;
        }

        return $archived;
    }

    /**
     * @param null|array<mixed> $spaces
     * @param bool              $noArchived
     *
     * @return array<mixed>
     */
    protected function prepareSpaceCsv(?array $spaces, bool $noArchived = true): array
    {
        return $this->prepareSpaceArray($spaces, $noArchived, true);
    }

    /**
     * @param null|array<mixed> $spaces
     * @param bool              $noArchived
     * @param bool              $asCsv
     *
     * @return array<mixed>
     */
    protected function prepareSpaceArray(?array $spaces, bool $noArchived = false, bool $asCsv = false): array // NOSONAR: php:S3776
    {
        $resultSpaces = [];

        if (is_array($spaces)) {
            if ($asCsv) {
                $idx = 0;
                foreach ($spaces as $space) {
                    if (is_array($space)) {
                        if ($this->isAddSpaceToList($space, $noArchived)) {
                            $resultSpaces[] = $space[ResponseParameter::KEY_KEY];
                            $this->printSpaceInfo($space, ++$idx);
                        } else {
                            self::$logger->notice('  ++ Space already archived', [$space[ResponseParameter::KEY_KEY]]);
                        }
                    }
                }
            } else {
                foreach ($spaces as $space) {
                    if (is_array($space)) {
                        $descr = $space[ResponseParameter::KEY_DESCRIPTION][ResponseParameter::KEY_PLAIN][ResponseParameter::KEY_VALUE];
                        $newSpace = [
                            ResponseParameter::KEY_ID => $space[ResponseParameter::KEY_ID],
                            ResponseParameter::KEY_KEY => $space[ResponseParameter::KEY_KEY],
                            ResponseParameter::KEY_NAME => $space[ResponseParameter::KEY_NAME],
                            ResponseParameter::KEY_TYPE => $space[ResponseParameter::KEY_TYPE],
                            ResponseParameter::KEY_HOMEPAGE => array_key_exists(ResponseParameter::KEY_HOMEPAGE, $space)
                                ? $space[ResponseParameter::KEY_HOMEPAGE][ResponseParameter::KEY_ID] : [],
                            ResponseParameter::KEY_ARCHIVED => $this->isArchived($descr) ? ResponseParameter::VAL_TRUE : ResponseParameter::VAL_FALSE,
                        ];

                        $resultSpaces[(string) $space[ResponseParameter::KEY_KEY]] = $newSpace;
                    }
                }
            }
            ksort($resultSpaces, SORT_FLAG_CASE | SORT_NATURAL);
        }

        return $resultSpaces;
    }

    protected function isAddSpaceToList(mixed $space, bool $noArchived): bool
    {
        $addResult = true;
        $descr = $space[ResponseParameter::KEY_DESCRIPTION][ResponseParameter::KEY_PLAIN][ResponseParameter::KEY_VALUE];
        if ($noArchived && $this->isArchived($descr)) {
            $addResult = false;
        }

        return $addResult;
    }

    protected function printSpaceInfo(mixed $space, int $idx): void
    {
        $descr = $space[ResponseParameter::KEY_DESCRIPTION][ResponseParameter::KEY_PLAIN][ResponseParameter::KEY_VALUE];

        $line = sprintf(
            '%s;%s;%s;%s',
            $idx,
            $space[ResponseParameter::KEY_KEY],
            $space[ResponseParameter::KEY_TYPE],
            $this->isArchived($descr) ? ResponseParameter::VAL_TRUE : ResponseParameter::VAL_FALSE
        );
        $line .= sprintf(
            ';\'%s\';\'%s\'',
            $space[ResponseParameter::KEY_NAME],
            htmlentities(implode('', explode(PHP_EOL, $descr)))
        );
        self::$logger->debug($line);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function __toStringValues(): mixed
    {
        return [ResponseParameter::KEY_SPACES => $this->spaces];
    }
}
