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

use Ds\Collection;
use Ds\Map;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\ConstData;
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
     * @return Collection<mixed,IExtension>
     *
     * @phpstan-return Map<mixed,IExtension>
     */
    public function publicLoadExtensions(ExtensionEnum $modeExtension): Collection
    {
        return $this->loadExtensions($modeExtension);
    }

    /**
     * @param ExtensionEnum $modeExtension
     *
     * @return Collection<mixed,IExtension>
     *
     * @phpstan-return Map<mixed,IExtension>
     */
    public function publicInitExtensions(ExtensionEnum $modeExtension): Collection
    {
        return $this->initExtensions($modeExtension);
    }

    /**
     * @param Collection<mixed,IExtension> $extensions
     *
     * @phpstan-param Map<mixed,IExtension> $extensions
     *
     * @return Collection<mixed,Vector<mixed>>
     *
     * @phpstan-return Map<mixed,Vector<mixed>>
     */
    public function publicGetExtensionAddons(Collection $extensions): Collection
    {
        return $this->getExtensionAddons($extensions);
    }

    /**
     * @param Collection<mixed,Vector<mixed>> $addons
     *
     * @phpstan-param Map<mixed,Vector<mixed>> $addons
     *
     * @return Vector<string>
     */
    public function publicGetExtensionAddonMacros(Collection $addons): Vector
    {
        return $this->getExtensionAddonMacros($addons);
    }

    /**
     * @param Collection<mixed,Vector<mixed>> $addons
     *
     * @phpstan-param Map<mixed,Vector<mixed>> $addons
     *
     * @return array<mixed,mixed>
     */
    public function publicGetExtensionAddonMacrosArray(Collection $addons): array
    {
        return $this->getExtensionAddonMacrosArray($addons);
    }

    public function publiGetExtension(ExtensionEnum $extension): ?IExtension
    {
        return $this->getExtension($extension);
    }
}
