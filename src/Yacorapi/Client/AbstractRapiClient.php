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
use Monolog\ConsoleLogger;
use oglow\tools\Addon\Atlassian\Extension\AdminExtension;
use oglow\tools\Addon\Atlassian\Extension\AtlassianExtension;
use oglow\tools\Addon\Projectdoc\Extension\ProjectdocExtension;
use oglow\tools\Addon\ThirdParty\Extension\ThirdPartyExtension;
use oglow\tools\Addon\UserMacro\Extension\UserMacroExtension;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\AddonMacroData;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\ExitCodes;
use oglow\tools\Yacorapi\Extension\IExtension;
use oglow\tools\Yacorapi\Extension\RapiClientExtension;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Provider\CurlProvider;
use oglow\tools\Yacorapi\Request\RequestType;
use oglow\tools\Yacorapi\Response\ResponseDryRun;
use oglow\tools\Yacorapi\Statistic\IStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticTypeEnum;
use oglow\tools\Yacorapi\Statistic\ValueStatistic;
use oglow\tools\Yacorapi\Traits\ExtensionTrait;
use ollily\Tools\Emergency;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class AbstractRapiClient implements IRapiClient
{
    use ExtensionTrait;

    /** Default output level (INFO) */
    public const string LEVEL_DEFAULT = LogLevel::INFO;

    public const string MSG_PARENT_ID_MUST_BE_NUMERIC = 'parentId must be numeric!';

    public const string MSG_MOVED_TO_NEW_PARENT = 'Page moved to new parent ';

    public const string MSG_SPACE_IS_EMPTY = 'spaceKey is empty!';

    public const string MSG_UPDATE_PAGE_WITHOUT_CHANGES = 'Update page without changes';

    protected ConstData $constData;

    protected AddonMacroData $addons;

    protected RapiClientExtension $commonExtension;

    protected AdminExtension $adminExtension;

    protected AtlassianExtension $atlassianExtension;

    protected UserMacroExtension $userMacroExtension;

    protected ThirdPartyExtension $thirdPartyExtension;

    protected ProjectdocExtension $projectdocExtension;

    protected IConnectionProvider $connectionProvider;

    private static LoggerInterface $logger;

    /**
     * Create new RapiClient.
     *
     * @param null|int                 $modeExtension
     * @param null|IConnectionProvider $connectionProvider
     * @param null|AddonMacroData      $addons
     *
     * @return IRapiClient
     */
    abstract public static function newClient(
        ?int $modeExtension = null,
        ?IConnectionProvider $connectionProvider = null,
        ?AddonMacroData $addons = null
    ): IRapiClient;

    /**
     * RapiClient constructor.
     *
     * @param null|int                 $modeExtension
     * @param null|IConnectionProvider $connectionProvider
     * @param null|AddonMacroData      $addons
     */
    protected function __construct(?int $modeExtension = null, ?IConnectionProvider $connectionProvider = null, ?AddonMacroData $addons = null)
    {
        // Init Logger
        self::$logger = new ConsoleLogger(AbstractRapiClient::class);
        self::$logger->debug('START');

        // Init Dynamic Consts
        $this->constData = new ConstData(get_class($this));
        // Init Modules
        if (is_null($modeExtension)) {
            $modeExtension = IExtension::EXTENSION_ALL;
        }
        if (empty($addons)) {
            $this->addons = new AddonMacroData();
        } else {
            $this->addons = $addons;
        }
        if (empty($connectionProvider)) {
            $this->connectionProvider = new CurlProvider(new ResponseDryRun(), self::LEVEL_DEFAULT);
        } else {
            $this->connectionProvider = $connectionProvider;
        }
        // Init Extensions
        $this->loadExtensions($modeExtension);

        self::$logger->debug('END');
    }

    /**
     * @param string $prepareUrl
     * @param int    $reqType
     *
     * @return IResponse
     */
    protected function exec(string $prepareUrl, int $reqType = RequestType::REQ_TYP_GET): IResponse
    {
        self::$logger->debug('START', [$prepareUrl, $reqType]);

        $response = $this->connectionProvider->exec($prepareUrl, $reqType);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @param string           $prepareUrl
     * @param Map<mixed,mixed> $parameters
     * @param int              $reqType
     *
     * @return IResponse
     */
    protected function execPost(string $prepareUrl, Map $parameters, int $reqType): IResponse
    {
        self::$logger->debug('START - prepareUrl,parameters,reqType', [$prepareUrl, $parameters, $reqType]);

        $response = $this->connectionProvider->execPost($prepareUrl, $parameters, $reqType);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @param array<mixed,mixed> $page
     *
     * @return string
     */
    protected function getSpaceKeyFromResult(array $page = []): string
    {
        return $page[RequestParameterData::PROP_CONTENT][RequestParameterData::PROP_SPACE][RequestParameterData::PROP_KEY] ??
        $page[RequestParameterData::PROP_SPACE][RequestParameterData::PROP_KEY];
    }

    /**
     * @param null|IStatistic $spaceResult
     * @param string          $spaceKey
     * @param string          $addon
     * @param string          $macroName
     * @param int             $macroCount
     *
     * @return IStatistic
     */
    protected function prepareMatrix(?IStatistic $spaceResult, string $spaceKey, string $addon, string $macroName, int $macroCount): IStatistic
    {
        self::$logger->info('START - spaceKey,addon,macroName,macroCount', [$spaceKey, $addon, $macroName, $macroCount]);

        if (empty($spaceResult)) {
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
     * @param string          $spaceKey
     * @param string          $addOn
     * @param Vector<string>  $macroNames
     * @param null|IStatistic $outputMatrix
     *
     * @return IStatistic
     */
    protected function loopAddonMacros(string $spaceKey, string $addOn, Vector $macroNames, ?IStatistic $outputMatrix): IStatistic
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
        if (empty($outputMatrix)) {
            $outputMatrix = new StatisticStatistic($spaceKey, StatisticTypeEnum::SPACE);
        }

        self::$logger->debug('END');

        return $outputMatrix;
    }

    /**
     * @param string            $spaceKey
     * @param int               $mode
     * @param Map<mixed, mixed> $mapAddons
     * @param null|IStatistic   $outputMatrix
     *
     * @return IStatistic
     */
    protected function loopAddons(string $spaceKey, int $mode, Map $mapAddons, ?IStatistic $outputMatrix): IStatistic
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
        if (empty($outputMatrix)) {
            $outputMatrix = new StatisticStatistic($spaceKey, StatisticTypeEnum::SPACE);
        }
        self::$logger->debug('END');

        return $outputMatrix;
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
}
