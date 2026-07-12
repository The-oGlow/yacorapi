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
use Ds\Set;
use Monolog\ConsoleLogger;
use oglow\tools\Addon\Atlassian\Extension\AdminExtension;
use oglow\tools\Addon\Atlassian\Extension\AtlassianExtension;
use oglow\tools\Addon\Projectdoc\Extension\ProjectdocExtension;
use oglow\tools\Addon\ThirdParty\Extension\ThirdPartyExtension;
use oglow\tools\Addon\UserMacro\Extension\UserMacroExtension;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\AddonMacroData;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\Extension\IExtension;
use oglow\tools\Yacorapi\Extension\RapiClientExtension;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Provider\CurlProvider;
use oglow\tools\Yacorapi\Request\RequestType;
use oglow\tools\Yacorapi\Response\ResponseDryRun;
use oglow\tools\Yacorapi\Traits\ExtensionTrait;
use ollily\Tools\Reflection\MagicPublicFunctionTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class AbstractRapiClient implements IRapiClient
{
    use ExtensionTrait;
    use RapiExtensionTrait;
    use MagicPublicFunctionTrait;

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
     * @param int|LogLevel|string      $level              (Default: {@link self::LEVEL_DEFAULT})
     *
     * @return IRapiClient
     *
     * @see self::LEVEL_DEFAULT
     */
    abstract public static function newClient(
        ?int $modeExtension = null,
        ?IConnectionProvider $connectionProvider = null,
        ?AddonMacroData $addons = null,
        mixed $level = self::LEVEL_DEFAULT
    ): IRapiClient;

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function taskitemMethods(): Set
    {
        return self::existingMethodNames();
    }

    /**
     * RapiClient constructor.
     *
     * @param null|int                        $modeExtension
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|AddonMacroData             $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link self::LEVEL_DEFAULT})
     *
     * @see self::LEVEL_DEFAULT
     */
    protected function __construct(
        ?int $modeExtension = null,
        ?IConnectionProvider $connectionProvider = null,
        ?AddonMacroData $addons = null,
        mixed $level = self::LEVEL_DEFAULT
    ) {
        // Init Logger
        /** @psalm-suppress ArgumentTypeCoercion @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name:AbstractRapiClient::class, level:$level);
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
            /** @psalm-suppress ArgumentTypeCoercion @phpstan-ignore argument.type */
            $this->connectionProvider = new CurlProvider(new ResponseDryRun(), $level);
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
}
