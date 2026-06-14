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

namespace oglow\tools\Yacorapi\Data;

use Ds\Map;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\common\AbstractContainer;
use oglow\tools\Yacorapi\ExitCodes;
use oglow\tools\Yacorapi\Macro\AllAddon;
use oglow\tools\Yacorapi\Macro\BlockerAddon;
use oglow\tools\Yacorapi\Macro\SingleAddon;
use oglow\tools\Yacorapi\Traits\ExtensionTrait;
use ollily\Tools\Emergency;
use Psr\Log\LoggerInterface;

class AddonMacroData extends AbstractContainer
{
    use ExtensionTrait;

    protected const int SHOW_ITEMS_MAX = 20;

    private static LoggerInterface $logger;

    public function __construct()
    {
        parent::__construct();
        self::$logger = new ConsoleLogger(AddonMacroData::class);
        self::$logger->debug('START');
    }

    /**
     * @param int|string $mode
     *
     * @return mixed
     */
    public function getMacros(int|string $mode = SingleAddon::ADDON_SINGLE): mixed
    {
        self::$logger->debug('START', [$mode]);
        $macros = [];
        if ($this->keyExists($mode)) {
            $addons = $this->getDataByMode($mode);
            if ($addons instanceof Map) {
                $macros = $this->getExtensionAddonMacrosArray($addons);
            } else {
                self::$logger->warning('Addons have wrong type', [(empty($addons) ? 'null' : AddonMacroData::class)]);
            }
        }
        if (count($macros) > self::SHOW_ITEMS_MAX) {
            self::$logger->debug('macros count', [count($macros)]);
        } else {
            self::$logger->debug('macros', [$macros]);
        }
        self::$logger->debug('END', [$mode]);

        return $macros;
    }

    /**
     * @param int|string $mode
     * @param string     $addon
     *
     * @return array<mixed,mixed>
     */
    public function getMacroNamesByAddon(int|string $mode, string $addon): array
    {
        self::$logger->debug('START', [$mode, $addon]);

        $macroNames = [];
        if ($this->keyExists($mode)) {
            $addons = $this->getDataByMode($mode);

            if ($addons instanceof Map) {
                $vecMacros = $addons->get($addon, []);
                foreach ($vecMacros as $macro) {
                    $macroNames[] = $macro;
                }
            } else {
                self::$logger->warning('Addons have wrong type', [(empty($addons) ? 'null' : AddonMacroData::class)]);
            }
        }
        if (count($macroNames) > self::SHOW_ITEMS_MAX) {
            self::$logger->debug('macroNames count', [count($macroNames)]);
        } else {
            self::$logger->debug('macroNames', [$macroNames]);
        }
        self::$logger->debug('END', [$mode, $addon]);

        return $macroNames;
    }

    /**
     * @param int|string $mode
     *
     * @return array<mixed,mixed>
     */
    public function getMacroNamesByMode(int|string $mode = SingleAddon::ADDON_SINGLE): array
    {
        self::$logger->debug('START', [$mode]);

        $macroNames = [];
        if ($this->keyExists($mode)) {
            $addons = $this->getDataByMode($mode);
            foreach ($addons as $addon) {
                if ($addon instanceof Vector) {
                    $macroNames = array_merge($macroNames, $addon->toArray());
                } else {
                    self::$logger->warning('Addon have wrong type', [(empty($addon) ? 'null' : AddonMacroData::class)]);
                }
            }
        }
        if (count($macroNames) > self::SHOW_ITEMS_MAX) {
            self::$logger->debug('macroNames count', [count($macroNames)]);
        } else {
            self::$logger->debug('macroNames', [$macroNames]);
        }
        self::$logger->debug('END', [$mode]);

        return $macroNames;
    }

    protected function prepareModes(): void
    {
        $allModes = [SingleAddon::ADDON_SINGLE, BlockerAddon::ADDON_BLOCKER, AllAddon::ADDON_ALL];
        $this->setModes($allModes);
    }

    protected function prepareData(): void
    {
        $allData = [];

        try {
            $singleAddon                        = new SingleAddon();
            $allData[SingleAddon::ADDON_SINGLE] = $singleAddon->getAddons();
        } catch (\Exception $ex) {
            Emergency::breakSystem(ExitCodes::ERR_CODE_SINGLEADDON_NOT_INIT, sprintf('SingleAddon failed: %s', $ex->getMessage()));
        }

        try {
            $blockerAddon                         = new BlockerAddon();
            $allData[BlockerAddon::ADDON_BLOCKER] = $blockerAddon->getAddons();
        } catch (\Exception $ex) {
            Emergency::breakSystem(ExitCodes::ERR_CODE_BLOCKER_ADDON_NOT_INIT, sprintf('BlockerAddon failed: %s', $ex->getMessage()));
        }

        try {
            $allAddon                     = new AllAddon();
            $allData[AllAddon::ADDON_ALL] = $allAddon->getAddons();
        } catch (\Exception $ex) {
            Emergency::breakSystem(ExitCodes::ERR_CODE_ALLADDON_NOT_INIT, sprintf('AllAddon failed: %s', $ex->getMessage()));
        }

        $this->setAllData($allData);
    }
}
