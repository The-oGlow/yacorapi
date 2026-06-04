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

use Monolog\ConsoleLogger;
use Psr\Log\LoggerInterface;

class ResponseAddonMacroDecorate extends AbstractResponse
{
    private static LoggerInterface $logger;

    private int $mode;

    /**
     * ResponseAddonMacroDecorate constructor.
     *
     * @param int                     $mode
     * @param null|array<mixed,mixed> $data
     */
    public function __construct(int $mode, ?array $data = null)
    {
        self::$logger = new ConsoleLogger(ResponseAddonMacroDecorate::class);
        self::$logger->debug('START');
        parent::__construct($data);
        $this->mode = $mode;
        self::$logger->debug('mode', [$this->getMode()]);
        self::$logger->debug('addons:', [ print_r($this->getResponse(), true)]);
        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    public function getResult(int $idx): void
    {
        throw new \BadFunctionCallException('Use instead ResponseAddonMacroDecorate->getValue()');
    }

    /**
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }
}
