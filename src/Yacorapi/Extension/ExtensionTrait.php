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
// use oglow\tools\Addon\Atlassian\Extension\AdminExtension;
// use oglow\tools\Addon\Atlassian\Extension\AtlassianExtension;
// use oglow\tools\Addon\Projectdoc\Extension\ProjectdocExtension;
// use oglow\tools\Addon\ThirdParty\Extension\ThirdPartyExtension;
// use oglow\tools\Addon\UserMacro\Extension\UserMacroExtension;
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

    //    protected RapiClientExtension $commonExtension;
    //
    //    protected AdminExtension $adminExtension;
    //
    //    protected AtlassianExtension $atlassianExtension;
    //
    //    protected UserMacroExtension $userMacroExtension;
    //
    //    protected ThirdPartyExtension $thirdPartyExtension;
    //
    //    protected ProjectdocExtension $projectdocExtension;

    /** @var Map<mixed,IExtension> */
    protected Map $loadedExtensions;

    /**
     * @param Map<mixed,Vector<mixed>> $addons
     *
     * @return Vector<mixed>
     */
    public function getExtensionAddonMacros(Map $addons): Vector
    {
        $macros = new Vector();

        /** @var Vector<string> $vecMacros */
        foreach ($addons->values() as $vecMacros) {
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
     * @return Map<mixed,IExtension>
     */
    protected function loadExtensions(ExtensionEnum $modeExtension): Map
    {
        self::$logger->debug('START', [$modeExtension]);

        $this->loadedExtensions = $this->initExtensions($modeExtension);

        //        $extensions = $this->initExtensions($modeExtension);
        //        foreach ($extensions as $key => $extension) {
        //            self::$logger->debug('Key,Ext', [$key]);
        //            switch ($key) {
        //                case ExtensionEnum::EXTENSION_RAPI_CLIENT->value:
        //                    //                    $this->commonExtension = $extension; // @phpstan-ignore assign.propertyType
        //                    break;
        //                case ExtensionEnum::EXTENSION_ATLASSIAN->value:
        //                    $this->atlassianExtension = $extension; // @phpstan-ignore assign.propertyType
        //                    break;
        //                case ExtensionEnum::EXTENSION_ATLASSIAN_ADMIN->value:
        //                    $this->adminExtension = $extension; // @phpstan-ignore assign.propertyType
        //                    break;
        //                case ExtensionEnum::EXTENSION_ATLASSIAN_USER_MACRO->value:
        //                    $this->userMacroExtension = $extension; // @phpstan-ignore assign.propertyType
        //                    break;
        //                case ExtensionEnum::EXTENSION_THIRD_PARTY->value:
        //                    $this->thirdPartyExtension = $extension; // @phpstan-ignore assign.propertyType
        //                    break;
        //                case ExtensionEnum::EXTENSION_PROJECTDOC_TOOLBOX->value:
        //                    $this->projectdocExtension = $extension; // @phpstan-ignore assign.propertyType
        //                    break;
        //                default:
        //                    Emergency::breakSystem(ExitCodes::ERR_CODE_EXTENSION_NOT_LOADED, sprintf('Extension not loaded: %s, %s ', $key, print_r($extension, true)));
        //            }
        //        }
        self::$logger->debug('END');

        return $this->loadedExtensions;
    }

    /**
     * Init extensions into a map.
     *
     * @param ExtensionEnum $modeExtension
     *
     * @return Map<mixed,IExtension>
     */
    protected function initExtensions(ExtensionEnum $modeExtension): Map
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
                //                self::$logger->notice('Extension not loaded', [$extensionEnum->name]);
                Emergency::breakSystem(ExitCodes::ERR_CODE_EXTENSION_NOT_LOADED, sprintf('Extension not loaded: %s ', $extensionEnum->name));
            }
        }

        self::$logger->debug('END');

        return $extensions;
    }

    /**
     * Returns a map of all addons (incl. macros) from all extensions.
     *
     * @param Map<mixed,IExtension> $extensions
     *
     * @return Map<mixed,Vector<mixed>>
     */
    protected function getExtensionAddons(Map $extensions): Map
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
     * @param Map<mixed,Vector<mixed>> $addons
     *
     * @return array<mixed,mixed>
     */
    protected function getExtensionAddonMacrosArray(Map $addons): array
    {
        $macros = $this->getExtensionAddonMacros($addons);

        return $macros->toArray();
    }
}
