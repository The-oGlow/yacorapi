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

use Ds\Collection;
use Monolog\ConsoleLogger;
use oglow\tools\common\AbstractContainer;
use oglow\tools\Yacorapi\ExitCodes;
use oglow\tools\Yacorapi\Extension\ExtensionTrait;
use ollily\Tools\Emergency;
use Psr\Log\LoggerInterface;
use Exception;
class AddonMacroData extends AbstractContainer
{

    use ExtensionTrait;

    protected const int VAL_SHOW_ITEMS_MAX = 20;

    private static LoggerInterface $logger;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(AddonMacroData::class);
        self::$logger->debug('START');
        parent::__construct();
        self::$logger->debug('END');
    }

    /**
     * @param AddonTypeEnum $mode
     *
     * @return mixed
     */
    public function getMacros(AddonTypeEnum $mode = AddonTypeEnum::ADDON_SINGLE): mixed
    {
        self::$logger->debug('START', [$mode]);

        $macros = [];
        if ($this->keyExists($mode->value)) {
            $addons = $this->getDataByMode($mode->value);
            if ($addons instanceof Collection) {
                $macros = $this->getExtensionAddonMacrosArray($addons);
            } else {
                self::$logger->warning('Addons have wrong datatype', [empty($addons) ? 'null' : gettype($addons)]);
            }
        }
        if (count($macros) > self::VAL_SHOW_ITEMS_MAX) {
            self::$logger->debug('macros count', [count($macros)]);
        } else {
            self::$logger->debug('macros', [$macros]);
        }
        self::$logger->debug('END', [$mode]);

        return $macros;
    }

    /**
     * @param AddonTypeEnum $mode
     * @param string        $addon
     *
     * @return array<mixed,mixed>
     */
    public function getMacroNamesByAddon(AddonTypeEnum $mode, string $addon): array
    {
        self::$logger->debug('START', [$mode, $addon]);

        $macroNames = [];
        if ($this->keyExists($mode->value)) {
            $addons = $this->getDataByMode($mode->value);
            if ($addons instanceof Collection) {
                $macros = $addons->get($addon, []);
                foreach ($macros as $macro) {
                    $macroNames[] = $macro;
                }
            } else {
                self::$logger->warning('Addons have wrong datatype', [empty($addons) ? 'null' : gettype($addons)]);
            }
        }
        if (count($macroNames) > self::VAL_SHOW_ITEMS_MAX) {
            self::$logger->debug('macroNames count', [count($macroNames)]);
        } else {
            self::$logger->debug('macroNames', [$macroNames]);
        }
        self::$logger->debug('END', [$mode, $addon]);

        return $macroNames;
    }

    /**
     * @param AddonTypeEnum $mode
     *
     * @return array<mixed,mixed>
     */
    public function getMacroNamesByMode(AddonTypeEnum $mode = AddonTypeEnum::ADDON_SINGLE): array
    {
        self::$logger->debug('START', [$mode]);

        $macroNames = [];
        if ($this->keyExists($mode->value)) {
            $addons = $this->getDataByMode($mode->value);
            if ($addons instanceof Collection) {
                foreach ($addons as $macros) {
                    if ($macros instanceof Collection) {
                        $macroNames = array_merge($macroNames, $macros->toArray());
                    } elseif (is_array($macros)) {
                        $macroNames = array_merge($macroNames, $macros);
                    } else {
                        self::$logger->warning('Macros have wrong datatype', [empty($macros) ? 'null' : gettype($macros)]);
                    }
                }
            } else {
                self::$logger->warning('Addons have wrong datatype', [empty($macros) ? 'null' : gettype($macros)]);
            }
        }
        if (count($macroNames) > self::VAL_SHOW_ITEMS_MAX) {
            self::$logger->debug('macroNames count', [count($macroNames)]);
        } else {
            self::$logger->debug('macroNames', [$macroNames]);
        }
        self::$logger->debug('END', [$mode]);

        return $macroNames;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareModes(): void
    {
        $allModes = [AddonTypeEnum::ADDON_SINGLE->value, AddonTypeEnum::ADDON_BLOCKER->value, AddonTypeEnum::ADDON_ALL->value];
        $this->setModes($allModes);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareData(): void
    {
        $allData = [];

        try {
            $singleAddon = new SingleAddon();
            $allData[AddonTypeEnum::ADDON_SINGLE->value] = $singleAddon->getAddons();
        } catch (Exception $ex) {
            Emergency::breakSystem(ExitCodes::ERR_CODE_SINGLEADDON_NOT_INIT, sprintf('SingleAddon failed: %s', $ex->getMessage()));
        }

        try {
            $blockerAddon = new BlockerAddon();
            $allData[AddonTypeEnum::ADDON_BLOCKER->value] = $blockerAddon->getAddons();
        } catch (Exception $ex) {
            Emergency::breakSystem(ExitCodes::ERR_CODE_BLOCKER_ADDON_NOT_INIT, sprintf('BlockerAddon failed: %s', $ex->getMessage()));
        }

        try {
            $allAddon = new AllAddon();
            $allData[AddonTypeEnum::ADDON_ALL->value] = $allAddon->getAddons();
        } catch (Exception $ex) {
            Emergency::breakSystem(ExitCodes::ERR_CODE_ALLADDON_NOT_INIT, sprintf('AllAddon failed: %s', $ex->getMessage()));
        }

        $this->setAllData($allData);
    }
}
