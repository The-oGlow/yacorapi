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

namespace oglow\tools\Yacorapi\Extension;

use Ds\Map;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Macro\IAddon;
use Psr\Log\LoggerInterface;

abstract class AbstractExtension implements IExtension
{
    /** @var LoggerInterface */
    private static $logger;

    /** @var ConstData */
    protected $constData;

    /** @var null|IAddon */
    protected $addons;

    public function __construct()
    {
        $clazzName = get_class($this);
        self::$logger = new ConsoleLogger(AbstractExtension::class);
        self::$logger->debug('START');
        $this->constData = new ConstData($clazzName);
        $this->init();
        self::$logger->debug('Is initiated', [$clazzName]);
        self::$logger->debug('END');
    }

    /**
     * @inheritdoc
     */
    abstract public static function getName(): string;

    /**
     * @inheritdoc
     */
    abstract public static function getId(): int;

    protected function init(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function getAddons(): Map
    {
        /** @var Map<mixed,Vector<mixed>> */
        $addonsTmp = new Map();
        if (!is_null($this->addons)) {
            $addonsTmp = $this->addons->getAddons();
        }

        return $addonsTmp;
    }

    /**
     * @inheritdoc
     */
    public function getMacros(): Vector
    {
        /** @var Vector<mixed> $macrosTmp */
        $macrosTmp = new Vector();
        if (!is_null($this->addons)) {
            $macrosTmp = $this->addons->getMacros();
        }

        return $macrosTmp;
    }
}
