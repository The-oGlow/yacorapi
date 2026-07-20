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
use oglow\tools\Yacorapi\Macro\AddonTypeEnum;
use Psr\Log\LoggerInterface;

class ResponseAddonMacroDecorate extends AbstractResponse
{
    private static LoggerInterface $logger;

    private AddonTypeEnum $mode;

    /**
     * ResponseAddonMacroDecorate constructor.
     *
     * @param AddonTypeEnum      $mode
     * @param array<mixed,mixed> $data
     */
    public function __construct(AddonTypeEnum $mode, array $data = [])
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
    #[\Override]
    public function getResult(int $idx): mixed
    {
        throw new \BadFunctionCallException('Use instead ResponseAddonMacroDecorate->getValue()');
    }

    /**
     * @return AddonTypeEnum
     */
    public function getMode(): AddonTypeEnum
    {
        return $this->mode;
    }
}
