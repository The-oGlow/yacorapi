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
use oglow\tools\Yacorapi\ExitCodes;
use ollily\Tools\Emergency;

/**
 * @phpstan-type ExtensionType null|IExtension|AdminExtension|AtlassianExtension|UserMacroExtension|ThirdPartyExtension|ProjectdocExtension
 */
trait ExtensionTrait
{
    protected const EXTENSION_AVAIL = [ExtensionEnum::EXTENSION_RAPI_CLIENT,
        ExtensionEnum::EXTENSION_ATLASSIAN,
        ExtensionEnum::EXTENSION_ATLASSIAN_ADMIN,
        ExtensionEnum::EXTENSION_ATLASSIAN_USER_MACRO,
        ExtensionEnum::EXTENSION_THIRD_PARTY,
        ExtensionEnum::EXTENSION_PROJECTDOC_TOOLBOX,
    ];

    /** @var Collection<mixed,IExtension>
     * @phpstan-var Map<mixed,IExtension> */
    protected Collection $loadedExtensions;

    /**
     * @param Collection<mixed,Vector<mixed>> $addons
     *
     * @phpstan-param Map<mixed,Vector<mixed>> $addons
     *
     * @return Vector<mixed>
     */
    public function getExtensionAddonMacros(Collection $addons): Vector
    {
        $macros = new Vector();

        /** @var Vector<string> $vecMacros */
        foreach (array_values($addons->toArray()) as $vecMacros) {
            foreach ($vecMacros as $macro) {
                $macros->push($macro);
            }
        }

        return $macros;
    }

    /**
     * Return an extension.
     *
     * @param ExtensionEnum $extension
     *
     * @return null|IExtension
     */
    protected function getExtension(ExtensionEnum $extension): ?IExtension
    {
        $result = null;
        $key = $extension->value;
        if ($this->loadedExtensions->hasKey($key)) {
            $result = $this->loadedExtensions->get($key);
        }

        return $result;
    }

    /**
     * Load extensions and set them to an field variable.
     *
     * @param ExtensionEnum $modeExtension
     *
     * @return Collection<mixed,IExtension>
     *
     * @phpstan-return Map<mixed,IExtension>
     */
    protected function loadExtensions(ExtensionEnum $modeExtension): Collection
    {
        self::$logger->debug('START', [$modeExtension]);

        $this->loadedExtensions = $this->initExtensions($modeExtension);

        self::$logger->debug('END');

        return $this->loadedExtensions;
    }

    /**
     * Init extensions into a collection.
     *
     * @param ExtensionEnum $modeExtension
     *
     * @return Collection<mixed,IExtension>
     *
     * @phpstan-return Map<mixed,IExtension>
     */
    protected function initExtensions(ExtensionEnum $modeExtension): Collection
    {
        self::$logger->debug('START', [$modeExtension]);

        /** @var Map<mixed,IExtension> $extensions */
        $extensions = new Map();

        foreach (self::EXTENSION_AVAIL as $extensionEnum) {
            if ($extensionEnum->isIn($modeExtension)) {
                $newInstance = $extensionEnum->objectValue();
                if (!empty($newInstance)) {
                    $extensions->put($newInstance->getId(), $newInstance); // @phpstan-ignore staticMethod.dynamicCall
                }
            } else {
                Emergency::breakSystem(ExitCodes::ERR_CODE_EXTENSION_NOT_LOADED, sprintf('Extension not loaded: %s ', $extensionEnum->name));
            }
        }

        self::$logger->debug('END');

        return $extensions;
    }

    /**
     * Returns a collection of all addons (incl. macros) from all extensions.
     *
     * @param Collection<mixed,IExtension> $extensions
     *
     * @phpstan-param Map<mixed,IExtension> $extensions
     *
     * @return Collection<mixed,Vector<mixed>>
     *
     * @phpstan-return Map<mixed,Vector<mixed>>
     */
    protected function getExtensionAddons(Collection $extensions): Collection
    {
        self::$logger->debug('START');

        /** @var Map<mixed,Vector<mixed>> $extensionAddons */
        $extensionAddons = new Map();

        foreach ($extensions as $extension) {
            $addons = $extension->getAddons();
            if (!$addons->isEmpty()) {
                foreach ($addons as $addonKey => $addon) {
                    $extensionAddons->put($addonKey, $addon);
                }
            }
        }
        self::$logger->debug('END');

        return $extensionAddons;
    }

    /**
     * Returns an.
     *
     * @param Collection<mixed,Vector<mixed>> $addons
     *
     * @phpstan-param Map<mixed,Vector<mixed>> $addons
     *
     * @return array<mixed,mixed>
     */
    protected function getExtensionAddonMacrosArray(Collection $addons): array
    {
        $macros = $this->getExtensionAddonMacros($addons);

        return $macros->toArray();
    }
}
