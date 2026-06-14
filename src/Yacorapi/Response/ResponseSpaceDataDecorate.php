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
use Psr\Log\LoggerInterface;

class ResponseSpaceDataDecorate extends AbstractResponse
{
    public const string SPACE_ARCH_FLAG1  = '[archived]';

    public const string SPACE_ARCH_FLAG2  = '[archive]';

    private static LoggerInterface $logger;

    /** @var array<mixed,mixed> */
    private array $spaces;

    public function __construct(IResponse $response)
    {
        self::$logger = new ConsoleLogger(ResponseSpaceDataDecorate::class);
        self::$logger->debug('START');
        $data                    = $response->getResponse();
        $data->put(self::KEY_RESULTS, $response->getResults());
        parent::__construct($data->toArray());
        $this->spaces = $this->prepareSpaceArray($response->getResults()->toArray());
        self::$logger->debug('END');
    }

    /**
     * @return array<mixed,mixed>
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
    public function keys(): Set
    {
        return new Set(array_keys($this->spaces));
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
        return new Map($this->getSpaces());
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getResult(int $idx): void
    {
        throw new \BadFunctionCallException('Try instead ResponseSpaceDataDecorate->getValue()');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function isResultsAvailable(): bool
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
     * @param null|array<mixed,mixed> $spaces
     * @param bool                    $noArchived
     *
     * @return array<mixed,mixed>
     */
    protected function prepareSpaceCsv(?array $spaces, bool $noArchived = true): array
    {
        return $this->prepareSpaceArray($spaces, $noArchived, true);
    }

    /**
     * @param null|array<mixed,mixed> $spaces
     * @param bool                    $noArchived
     * @param bool                    $asCsv
     *
     * @return array<mixed,mixed>
     */
    protected function prepareSpaceArray(?array $spaces, bool $noArchived = false, bool $asCsv = false): array // NOSONAR: php:S3776
    {
        $resultSpaces = [];

        if (is_array($spaces)) {
            if ($asCsv) {
                $idx = 0;
                foreach ($spaces as $space) {
                    $line = '';
                    if (is_array($space)) {
                        $addResult = true;
                        $descr     = $space[self::KEY_DESCRIPTION][self::KEY_PLAIN][self::KEY_VALUE];
                        if ($noArchived && $this->isArchived($descr)) {
                            $addResult = false;
                        }
                        if ($addResult) {
                            $resultSpaces[] = $space[self::KEY_KEY];

                            $line .= sprintf(
                                '%s;%s;%s;%s',
                                ++$idx, // NOSONAR php:S881
                                $space[self::KEY_KEY],
                                $space[self::KEY_TYPE],
                                $this->isArchived($descr) ? self::VAL_TRUE : self::VAL_FALSE
                            );
                            $line .= sprintf(
                                ';\'%s\';\'%s\'',
                                $space[self::KEY_NAME],
                                htmlentities(
                                    implode(
                                        '',
                                        explode(
                                            PHP_EOL,
                                            $descr
                                        )
                                    )
                                )
                            );
                        } else {
                            self::$logger->notice('  ++ Space already archived', [$space[self::KEY_KEY]]);
                        }
                    }
                    self::$logger->debug($line);
                }
            } else {
                foreach ($spaces as $space) {
                    if (is_array($space)) {
                        $descr    = $space[self::KEY_DESCRIPTION][self::KEY_PLAIN][self::KEY_VALUE];
                        $newSpace = [
                            self::KEY_KEY      => $space[self::KEY_KEY],
                            self::KEY_NAME     => $space[self::KEY_NAME],
                            self::KEY_TYPE     => $space[self::KEY_TYPE],
                            self::KEY_ARCHIVED => $this->isArchived($descr) ? self::VAL_TRUE : self::VAL_FALSE,
                        ];

                        $resultSpaces[(string)$space[self::KEY_KEY]] = $newSpace;
                    }
                }
            }
            asort($resultSpaces);
        }

        return $resultSpaces;
    }

    /**
     * @inheritDoc
     *
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     */
    #[\Override]
    protected function __toStringValues(): mixed
    {
        return [self::KEY_SPACES => $this->spaces];
    }
}
