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

use Ds\Collection;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\QueryExtensionEnum;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Macro\AddonTypeEnum;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use oglow\tools\Yacorapi\Response\ResponseParameterData;
use oglow\tools\Yacorapi\Response\ResponseSpaceDataDecorate;
use oglow\tools\Yacorapi\Space\SpaceTypeEnum;
use oglow\tools\Yacorapi\Statistic\IStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticTypeEnum;
use oglow\tools\Yacorapi\Statistic\ValueStatistic;
use Psr\Log\LoggerInterface;

class RapiClientStatistic extends RapiClientPermission implements IRapiClientStatistic
{
    private static LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param null|ExtensionEnum              $modeExtension      (Default: {@link IRapiClientBase::EXTENSION_DEFAULT})
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|IContainer                 $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link IRapiClientBase::LEVEL_DEFAULT})
     */
    protected function __construct(
        ?ExtensionEnum $modeExtension = IRapiClientBase::EXTENSION_DEFAULT,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ) {
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name: RapiClientStatistic::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($modeExtension, $connectionProvider, $addons, $level);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function countItemsinSpace(string $spaceKey, ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE): IStatistic
    {
        self::$logger->debug('START - spaceKey, itemType', [$spaceKey, $itemType]);

        $prepareUrl = $this->prepareCountItemsUrl($itemType, $spaceKey);
        $response = $this->exec($prepareUrl);

        $itemCount = $response->getValue(ResponseParameterData::KEY_TOTAL_SIZE, 0);
        $valueStatistic = new ValueStatistic(ValueStatistic::EMPTY_STRING, null);
        $valueStatistic->addItem(ValueStatistic::EMPTY_STRING, $itemCount);
        $singleStatistic = new StatisticStatistic($itemType->value, StatisticTypeEnum::PAGETYPE);
        $singleStatistic->addItem($itemType->value, $valueStatistic);

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

        $mapAddons = $addonSet->getRawData();

        $response = $this->loopAddons($spaceKey, $addonSet->getMode(), $mapAddons, $outputMatrix);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function listSpaces(SpaceTypeEnum $spaceType = SpaceTypeEnum::SPACE_TYPE_GLOBAL, int $limit = IRapiClientBase::REQ_VAL_SPACE_LIMIT_DEFAULT): IResponse
    {
        self::$logger->debug('START - spaceType,limit', [$spaceType, $limit]);

        $prepareUrl = $this->prepareSpaceListUrl($spaceType, $limit);

        return new ResponseSpaceDataDecorate($this->exec($prepareUrl));
    }

    /**
     * @param IStatistic $spaceResult
     * @param string     $spaceKey
     * @param string     $addonName
     * @param string     $macroName
     * @param int        $macroCount
     *
     * @return IStatistic
     */
    protected function prepareMatrix(IStatistic $spaceResult, string $spaceKey, string $addonName, string $macroName, int $macroCount): IStatistic
    {
        self::$logger->debug('START - spaceKey,addonName,macroName,macroCount', [$spaceKey, $addonName, $macroName, $macroCount]);

        /** @psalm-suppress TypeDoesNotContainType
         * @phpstan-ignore empty.variable */
        if (empty($spaceResult)) {
            $spaceResult = new StatisticStatistic($spaceKey, StatisticTypeEnum::SPACE);
        }

        /** @var null|IStatistic */
        $addonResult = $spaceResult->getItem($addonName);
        if (empty($addonResult)) {
            $addonResult = new StatisticStatistic($addonName, StatisticTypeEnum::ADDON);
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
        } else {
            self::$logger->info(var_export($valueResult->getItem(ValueStatistic::EMPTY_STRING), true));
        }

        $value = $valueResult->getItem(ValueStatistic::EMPTY_STRING);
        if (is_numeric($value)) {
            $valueResult->addItem(ValueStatistic::EMPTY_STRING, $macroCount + intval($value));
        } else {
            $valueResult->addItem(ValueStatistic::EMPTY_STRING, $macroCount);
        }

        $macroResult->addItem(ValueStatistic::KEY_COUNT, $valueResult);
        $addonResult->addItem($macroName, $macroResult);
        $spaceResult->addItem($addonName, $addonResult);

        self::$logger->debug('END');

        return $spaceResult;
    }

    /**
     * @param string                  $spaceKey
     * @param string                  $addOnName
     * @param Collection<mixed,mixed> $macroNames
     * @param IStatistic              $outputMatrix
     *
     * @return IStatistic
     */
    protected function loopAddonMacros(string $spaceKey, string $addOnName, Collection $macroNames, IStatistic $outputMatrix): IStatistic
    {
        self::$logger->debug('START - spaceKey,addOnName,macroNames', [$spaceKey, $addOnName, $macroNames]);

        $cntMacros = count($macroNames);
        $cntIdx = 0;
        foreach ($macroNames as $macroName) {
            self::$logger->debug('Checking Space with Macro - START', [++$cntIdx, $cntMacros, $spaceKey, $addOnName, $macroName]);

            $searchTerm = "macroName:$macroName";
            $prepareUrl = $this->prepareSearchUrlExt(
                $searchTerm,
                $spaceKey,
                IRapiClientBase::REQ_VAL_SEARCH_START,
                IRapiClientBase::REQ_VAL_SEARCH_LIMIT_1ENTRY
            );
            $response = $this->exec($prepareUrl);
            $countMacros = $this->analyzeResponse($response);

            $outputMatrix = $this->prepareMatrix($outputMatrix, $spaceKey, $addOnName, $macroName, $countMacros);
            if ($countMacros > 0) {
                self::$logger->info('Found', [$cntIdx, $cntMacros, $spaceKey, $addOnName, $macroName, $countMacros]);
            } else {
                self::$logger->debug('Not Found', [$cntIdx, $cntMacros, $spaceKey, $addOnName, $macroName, $countMacros]);
            }

            self::$logger->debug('Checking Space with Macro - END');
        }
        /** @psalm-suppress TypeDoesNotContainType
         * @phpstan-ignore empty.variable */
        if (empty($outputMatrix)) {
            $outputMatrix = new StatisticStatistic($spaceKey, StatisticTypeEnum::SPACE);
        }

        self::$logger->debug('END');

        return $outputMatrix;
    }

    /**
     * @param string                   $spaceKey
     * @param AddonTypeEnum            $addonMode
     * @param Collection<mixed, mixed> $mapAddons
     * @param IStatistic               $outputMatrix
     *
     * @return IStatistic
     */
    protected function loopAddons(string $spaceKey, AddonTypeEnum $addonMode, Collection $mapAddons, IStatistic $outputMatrix): IStatistic
    {
        self::$logger->debug('START - spaceKey,mode,addonMode', [$spaceKey, $addonMode, $mapAddons]);

        $cntAddons = $mapAddons->count();
        $cntIdx = 0;
        foreach ($mapAddons as $addOnKey => $addonValue) {
            self::$logger->info('Checking Addon - START', [++$cntIdx, $cntAddons, $spaceKey, $addOnKey]);
            if (!is_array($addonValue)) {
                /** @psalm-suppress UndefinedInterfaceMethod
                 * @phpstan-ignore method.notFound */
                $macroNames = $this->addons->getMacroNamesByAddon($addonMode, $addOnKey);
                $addonName = $addOnKey;
            } else {
                $macroNames = $addonValue;
                $addonName = $addOnKey;
            }
            self::$logger->debug('Found :', [$addonName, $macroNames]);

            $outputMatrix = $this->loopAddonMacros($spaceKey, $addonName, new Vector($macroNames), $outputMatrix);

            self::$logger->debug('Checking Addon - END');
        }
        /** @psalm-suppress TypeDoesNotContainType
         * @phpstan-ignore empty.variable */
        if (empty($outputMatrix)) {
            $outputMatrix = new StatisticStatistic($spaceKey, StatisticTypeEnum::SPACE);
        }
        self::$logger->debug('END');

        return $outputMatrix;
    }

    protected function prepareSpacePagesUrl(
        string $space,
        ItemTypeEnum $pageType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE,
        int $start = ConstData::PAGE_START,
        int $limit = ConstData::PAGE_LIMIT
    ): string {
        return sprintf(
            '%s/%s/content/%s?start=%s&limit=%s&%s',
            $this->constData->c(ConstData::KEY_CONF_SPACE_URL),
            $space,
            $pageType->value,
            $start,
            $limit,
            QueryExtensionEnum::REQP_FULL->value
        );
    }

    protected function prepareSpaceListUrl(
        SpaceTypeEnum $spaceType = SpaceTypeEnum::SPACE_TYPE_GLOBAL,
        int $limit = ConstData::PAGE_LIMIT
    ): string {
        return sprintf(
            '%s?%s&type=%s&limit=%s',
            $this->constData->c(ConstData::KEY_CONF_SPACE_URL),
            QueryExtensionEnum::REQP_SPACE_LIST->value,
            $spaceType->value,
            $limit
        );
    }

    protected function prepareCountItemsUrl(ItemTypeEnum $itemType, string $spaceKey): string
    {
        return ((string) $this->constData->c(ConstData::KEY_CONF_SEARCH_URL)) . "?cql=type+in+(" . $itemType->value . ")+AND+space=$spaceKey";
    }
}
