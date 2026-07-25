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
use oglow\tools\Addon\Atlassian\Extension\AdminExtension;
use oglow\tools\Addon\Atlassian\Extension\AtlassianExtension;
use oglow\tools\Addon\Projectdoc\Extension\ProjectdocExtension;
use oglow\tools\Addon\ThirdParty\Extension\ThirdPartyExtension;
use oglow\tools\Addon\UserMacro\Extension\UserMacroExtension;
use oglow\tools\Yacorapi\ExitCodes;
use oglow\tools\Yacorapi\Extension\IExtension;
use oglow\tools\Yacorapi\Extension\RapiClientExtension;
use ollily\Tools\Emergency;

trait ExtensionTrait
{
    protected RapiClientExtension $commonExtension;

    protected AdminExtension $adminExtension;

    protected AtlassianExtension $atlassianExtension;

    protected UserMacroExtension $userMacroExtension;

    protected ThirdPartyExtension $thirdPartyExtension;

    protected ProjectdocExtension $projectdocExtension;

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
     * @param int $modeExtension
     */
    protected function loadExtensions(int $modeExtension): void
    {
        self::$logger->debug('START', [$modeExtension]);

        $extensions = $this->initExtensions($modeExtension);

        foreach ($extensions as $key => $extension) {
            self::$logger->debug('Key,Ext', [$key]);

            switch (true) {
                case IExtension::EXTENSION_RAPI_CLIENT == $key:
                    $this->commonExtension = $extension;
                    break;
                case IExtension::EXTENSION_ATLASSIAN == $key:
                    $this->atlassianExtension = $extension;
                    break;
                case IExtension::EXTENSION_ATLASSIAN_ADMIN == $key:
                    $this->adminExtension = $extension;
                    break;
                case IExtension::EXTENSION_ATLASSIAN_USER_MACRO == $key:
                    $this->userMacroExtension = $extension;
                    break;
                case IExtension::EXTENSION_THIRD_PARTY == $key:
                    $this->thirdPartyExtension = $extension;
                    break;
                case IExtension::EXTENSION_PROJECTDOC_TOOLBOX == $key:
                    $this->projectdocExtension = $extension;
                    break;
                default:
                    Emergency::breakSystem(ExitCodes::ERR_CODE_EXTENSION_NOT_LOADED, sprintf('Extension not loaded: %s, %s ', $key, print_r($extension, true)));
            }
        }
        self::$logger->debug('END');
    }

    /**
     * Init all extensions.
     *
     * @param int $modeExtension
     *
     * @return Map<mixed,IExtension|mixed>
     */
    protected function initExtensions(int $modeExtension): Map
    {
        self::$logger->debug('START', [$modeExtension]);

        $extensions = new Map();
        if ((IExtension::EXTENSION_RAPI_CLIENT & $modeExtension) == IExtension::EXTENSION_RAPI_CLIENT) {
            $extensions->put(RapiClientExtension::getId(), new RapiClientExtension());
        } else {
            self::$logger->notice('RapiClientExtension not loaded');
        }
        if ((IExtension::EXTENSION_ATLASSIAN & $modeExtension) == IExtension::EXTENSION_ATLASSIAN) {
            $extensions->put(AtlassianExtension::getId(), new AtlassianExtension());
        } else {
            self::$logger->notice('AtlassianExtension not loaded');
        }
        if ((IExtension::EXTENSION_ATLASSIAN_ADMIN & $modeExtension) == IExtension::EXTENSION_ATLASSIAN_ADMIN) {
            $extensions->put(AdminExtension::getId(), new AdminExtension());
        } else {
            self::$logger->notice('AdminExtension not loaded');
        }
        if ((IExtension::EXTENSION_ATLASSIAN_USER_MACRO & $modeExtension) == IExtension::EXTENSION_ATLASSIAN_USER_MACRO) {
            $extensions->put(UserMacroExtension::getId(), new UserMacroExtension());
        } else {
            self::$logger->notice('UserMacroExtension not loaded');
        }
        if ((IExtension::EXTENSION_THIRD_PARTY & $modeExtension) == IExtension::EXTENSION_THIRD_PARTY) {
            $extensions->put(ThirdPartyExtension::getId(), new ThirdPartyExtension());
        } else {
            self::$logger->notice('ThirdPartyMacro not loaded');
        }
        if ((IExtension::EXTENSION_PROJECTDOC_TOOLBOX & $modeExtension) == IExtension::EXTENSION_PROJECTDOC_TOOLBOX) {
            $extensions->put(ProjectdocExtension::getId(), new ProjectdocExtension());
        } else {
            self::$logger->notice('ProjectdocExtension not loaded');
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
