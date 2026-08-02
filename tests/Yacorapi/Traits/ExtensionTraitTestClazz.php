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

namespace oglow\tools\Yacorapi\Traits;

use Ds\Map;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\Extension\IExtension;
use Psr\Log\LoggerInterface;

class ExtensionTraitTestClazz
{
    use ExtensionTrait;

    protected ConstData $constData;

    private static LoggerInterface $logger;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(ExtensionTraitTestClazz::class);
        self::$logger->debug('START');
        $this->constData = new ConstData(ExtensionTraitTestClazz::class);
        self::$logger->debug('END');
    }

    /**
     * @param ExtensionEnum $modeExtension
     *
     * @return Map<mixed,IExtension>
     */
    public function publicInitExtensions(ExtensionEnum $modeExtension): Map
    {
        return $this->initExtensions($modeExtension);
    }

    /**
     * @param Map<mixed,IExtension> $extensions
     *
     * @return Map<mixed,Vector<mixed>>
     */
    public function publicGetExtensionAddons(Map $extensions): Map
    {
        return $this->getExtensionAddons($extensions);
    }

    /**
     * @param Map<mixed,Vector<mixed>> $addons
     *
     * @return Vector<string>
     */
    public function publicGetExtensionAddonMacros(Map $addons): Vector
    {
        return $this->getExtensionAddonMacros($addons);
    }

    /**
     * @param Map<mixed,Vector<mixed>> $addons
     *
     * @return array<mixed,mixed>
     */
    public function publicGetExtensionAddonMacrosArray(Map $addons): array
    {
        return $this->getExtensionAddonMacrosArray($addons);
    }
}
