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

namespace oglow\tools\Yacorapi\Macro;

use Ds\Map;
use Ds\Vector;
use Monolog\ConsoleLogger;
use Psr\Log\LoggerInterface;

abstract class AbstractAddon implements IAddon
{
    private static LoggerInterface $logger;

    /** @var Map<mixed,Vector<mixed>> */
    protected Map $addonsMacros;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(AbstractAddon::class);
        self::$logger->debug('START');
        $this->init();
        self::$logger->debug('Is initiated', [AbstractAddon::class]);
        self::$logger->debug('END');
    }

    protected function init(): void
    {
        $this->addonsMacros = new Map();
    }

    /**
     * @inheritDoc
     */
    public function getAddons(): Map
    {
        return $this->addonsMacros;
    }

    /**
     * @inheritDoc
     */
    public function getAddonNames(): Vector
    {
        return new Vector($this->addonsMacros->keys());
    }

    /**
     * @inheritDoc
     */
    public function getMacros(): Vector
    {
        $macros = new Vector();

        /** @var Vector<mixed> $vecMacros */
        foreach ($this->addonsMacros->values() as $vecMacros) {
            foreach ($vecMacros as $macro) {
                $macros->push($macro);
            }
        }

        return $macros;
    }

    /**
     * @inheritDoc
     */
    public function getMacrosArray(): array
    {
        return $this->getMacros()->toArray();
    }
}
