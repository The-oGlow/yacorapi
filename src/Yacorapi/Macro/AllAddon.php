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

use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\Extension\IExtension;
use oglow\tools\Yacorapi\Traits\ExtensionTrait;
use Psr\Log\LoggerInterface;

class AllAddon extends AbstractAddon
{
    use ExtensionTrait;

    public const ADDON_ALL = 99;

    private static LoggerInterface $logger;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(AllAddon::class);
        self::$logger->debug('START');
        parent::__construct();
        self::$logger->debug('END');
    }

    protected function init(): void
    {
        parent::init();
        $extensions         = $this->initExtensions(IExtension::EXTENSION_ALL);
        $this->addonsMacros = $this->getExtensionAddons($extensions);
    }
}
