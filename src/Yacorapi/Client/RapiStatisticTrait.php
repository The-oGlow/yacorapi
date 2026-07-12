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

use Ds\Map;
use Ds\Vector;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use oglow\tools\Yacorapi\Response\ResponseSpaceDataDecorate;
use oglow\tools\Yacorapi\Statistic\IStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticTypeEnum;
use oglow\tools\Yacorapi\Statistic\ValueStatistic;

trait RapiStatisticTrait
{
    /**
     * REFACTOR: Listing only 100 spaces, loop is missing.
     *
     * @inheritDoc
     */
    #[\Override]
    public function listSpaces(string $spaceType = RequestParameterData::SPACE_TYPE_GLOBAL, int $limit = RequestParameterData::SPACE_LIMIT_DEFAULT): IResponse
    {
        self::$logger->debug('START - spaceType,limit', [$spaceType, $limit]);

        $prepareUrl = $this->commonExtension->prepareSpaceListUrl($spaceType, $limit);

        return new ResponseSpaceDataDecorate($this->exec($prepareUrl));
    }

    /**
     * @param IStatistic $spaceResult
     * @param string     $spaceKey
     * @param string     $addon
     * @param string     $macroName
     * @param int        $macroCount
     *
     * @return IStatistic
     */
    protected function prepareMatrix(IStatistic $spaceResult, string $spaceKey, string $addon, string $macroName, int $macroCount): IStatistic
    {
        self::$logger->info('START - spaceKey,addon,macroName,macroCount', [$spaceKey, $addon, $macroName, $macroCount]);

        if (empty($spaceResult)) { // @phpstan-ignore empty.variable
            $spaceResult = new StatisticStatistic($spaceKey, StatisticTypeEnum::SPACE);
        }

        /** @var null|IStatistic */
        $addonResult = $spaceResult->getItem($addon);
        if (empty($addonResult)) {
            $addonResult = new StatisticStatistic($addon, StatisticTypeEnum::ADDON);
        }

        /** @var null|IStatistic */
        $macroResult = $addonResult->getItem($macroName);
        if (empty($macroResult)) {
            $macroResult = new StatisticStatistic($macroName, StatisticTypeEnum::MACRO);
        }

        /** @var null|ValueStatistic */
        $valueResult = $macroResult->getItem(ValueStatistic::KEY_COUNT);
        if (empty($valueResult)) {
            $valueResult = new ValueStatistic(ValueStatistic::EMPTY_STRING, null);
        }

        self::$logger->info(var_export($valueResult->getItem(ValueStatistic::EMPTY_STRING), true));

        $value = $valueResult->getItem(ValueStatistic::EMPTY_STRING);
        if (is_numeric($value)) {
            $valueResult->addItem(ValueStatistic::EMPTY_STRING, $macroCount + (int) $value);
        } else {
            $valueResult->addItem(ValueStatistic::EMPTY_STRING, $macroCount);
        }

        $macroResult->addItem(ValueStatistic::KEY_COUNT, $valueResult);
        $addonResult->addItem($macroName, $macroResult);
        $spaceResult->addItem($addon, $addonResult);

        self::$logger->debug('END');

        return $spaceResult;
    }

    /**
     * @param string         $spaceKey
     * @param string         $addOn
     * @param Vector<string> $macroNames
     * @param IStatistic     $outputMatrix
     *
     * @return IStatistic
     */
    protected function loopAddonMacros(string $spaceKey, string $addOn, Vector $macroNames, IStatistic $outputMatrix): IStatistic
    {
        self::$logger->debug('START - spaceKey,addOn,macroNames', [$spaceKey, $addOn, $macroNames]);

        $cntMacros = count($macroNames);
        $cntIdx = 0;
        foreach ($macroNames as $macroName) {
            self::$logger->debug('Checking Space with Macro - START', [++$cntIdx, $cntMacros, $spaceKey, $addOn, $macroName]);

            $searchTerm = "macroName:$macroName";
            $prepareUrl = $this->commonExtension->prepareSearchUrlExt(
                $searchTerm,
                $spaceKey,
                RequestParameterData::SEARCH_START,
                RequestParameterData::SEARCH_LIMIT_1ENTRY
            );
            $response = $this->exec($prepareUrl);
            $countMacros = $this->commonExtension->analyzeResponse($response);

            $outputMatrix = $this->prepareMatrix($outputMatrix, $spaceKey, $addOn, $macroName, $countMacros);
            self::$logger->info('Found', [$cntIdx, $cntMacros, $spaceKey, $addOn, $macroName, $countMacros]);

            self::$logger->debug('Checking Space with Macro - END');
        }
        if (empty($outputMatrix)) { // @phpstan-ignore empty.variable
            $outputMatrix = new StatisticStatistic($spaceKey, StatisticTypeEnum::SPACE);
        }

        self::$logger->debug('END');

        return $outputMatrix;
    }

    /**
     * @param string            $spaceKey
     * @param int               $mode
     * @param Map<mixed, mixed> $mapAddons
     * @param IStatistic        $outputMatrix
     *
     * @return IStatistic
     */
    protected function loopAddons(string $spaceKey, int $mode, Map $mapAddons, IStatistic $outputMatrix): IStatistic
    {
        self::$logger->debug('START - spaceKey,mode,addons', [$spaceKey, $mode, $mapAddons]);

        $cntAddons = count($mapAddons);
        $cntIdx = 0;
        foreach ($mapAddons as $addOnKey => $addonValue) {
            self::$logger->info('Checking Addon - START', [++$cntIdx, $cntAddons, $spaceKey, $addOnKey]);
            if (!is_array($addonValue)) {
                $macroNames = $this->addons->getMacroNamesByAddon($mode, $addOnKey);
                $addonName = $addOnKey;
            } else {
                $macroNames = $addonValue;
                $addonName = $addOnKey;
            }
            self::$logger->debug('Found :', [$addonName, $macroNames]);

            $outputMatrix = $this->loopAddonMacros($spaceKey, $addonName, new Vector($macroNames), $outputMatrix);

            self::$logger->debug('Checking Addon - END');
        }
        if (empty($outputMatrix)) { // @phpstan-ignore empty.variable
            $outputMatrix = new StatisticStatistic($spaceKey, StatisticTypeEnum::SPACE);
        }
        self::$logger->debug('END');

        return $outputMatrix;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function countItemsinSpace(string $spaceKey, string $itemType = RequestParameterData::ITEM_TYPE_PAGE): IStatistic
    {
        self::$logger->debug('START - spaceKey, itemType', [$spaceKey, $itemType]);

        $prepareUrl = $this->commonExtension->prepareCountItemsUrl($itemType, $spaceKey);
        $response = $this->exec($prepareUrl);

        $itemCount = $response->getValue(IResponse::KEY_TOTAL_SIZE, 0);
        $valueStatistic = new ValueStatistic(ValueStatistic::EMPTY_STRING, null);
        $valueStatistic->addItem(ValueStatistic::EMPTY_STRING, $itemCount);
        $singleStatistic = new StatisticStatistic($itemType, StatisticTypeEnum::PAGETYPE);
        $singleStatistic->addItem($itemType, $valueStatistic);

        self::$logger->debug('END');

        return $singleStatistic;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function countMacrosInSpace(string $spaceKey, ResponseAddonMacroDecorate $addonSet, IStatistic $outputMatrix): IStatistic
    {
        self::$logger->debug('START - spaceKey,addonSet', [$spaceKey, $addonSet]);

        $mapAddons = $addonSet->getResponse();

        $response = $this->loopAddons($spaceKey, $addonSet->getMode(), $mapAddons, $outputMatrix);

        self::$logger->debug('END');

        return $response;
    }
}
