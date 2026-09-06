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
use ollily\Tools\JsonHelper;
use ollily\Tools\Reflection\ClazzHelper;
use Exception;

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
        $this->write();
        self::$logger->debug('Is initiated', [AbstractAddon::class]);
        self::$logger->debug('END');
    }

    protected function init(): void
    {
        $this->addonsMacros = new Map();
        $file = ClazzHelper::getClazzPath(static::class) . DIRECTORY_SEPARATOR . ClazzHelper::getClazzFilename(static::class).  JsonHelper::FILE_EXT_JSON;
        if (file_exists($file)) {
            try {
                $this->addonsMacros = new Map(JsonHelper::loadJson($file));
            } catch (Exception $exception) {
                self::$logger->error($exception->getMessage());
            }
        } else {
            self::$logger->notice('No addon datafile to load', [$file]);
        }
    }

    protected function write(): void
    {
//        $file = 'c:\\projekte\\webdata\\tmp\\' . ClazzHelper::getClazzFilename(static::class);
//        JsonHelper::storeJsonCollection($this->addonsMacros, $file, JsonHelper::FILE_EXT_JSON, false);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getAddons(): Map
    {
        return $this->addonsMacros;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getAddonNames(): Vector
    {
        return new Vector($this->addonsMacros->keys());
    }

    /**
     * @inheritDoc
     */
    #[\Override]
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
    #[\Override]
    public function getMacrosArray(): array
    {
        return $this->getMacros()->toArray();
    }
}
