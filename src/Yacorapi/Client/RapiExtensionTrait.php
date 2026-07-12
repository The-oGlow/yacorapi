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

namespace oglow\tools\Yacorapi\Client;

use oglow\tools\Yacorapi\ExitCodes;
use oglow\tools\Yacorapi\Extension\IExtension;
use oglow\tools\Yacorapi\Macro\AllAddon;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use ollily\Tools\Emergency;

trait RapiExtensionTrait
{
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
     * @inheritDoc
     */
    #[\Override]
    public function prepareAddonSet($mode = AllAddon::ADDON_ALL): ResponseAddonMacroDecorate
    {
        self::$logger->debug('START - mode', [$mode]);

        $data = $this->addons->getDataByMode($mode);
        if (!empty($data)) {
            /** @psalm-suppress MixedMethodCall */
            $addonSet = new ResponseAddonMacroDecorate($mode, $data->toArray());
        } else {
            $addonSet = new ResponseAddonMacroDecorate($mode);
        }
        self::$logger->debug('END');

        return $addonSet;
    }
}
