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

use Ds\Set;
use Monolog\ConsoleLogger;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\Data\SpaceTypeEnum;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\Extension\ExtensionTrait;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Macro\AddonTypeEnum;
use oglow\tools\Yacorapi\Response\ResponseAddonMacroDecorate;
use oglow\tools\Yacorapi\Statistic\IStatistic;
use ollily\Tools\Batch\ITaskList;
use Psr\Log\LoggerInterface;

/**
 * RapiClient.
 *
 * <i>Read Methods</i>
 *
 * @method ResponseAddonMacroDecorate prepareAddonSet(AddonTypeEnum $mode = AddonTypeEnum::ADDON_ALL)
 * @method IResponse                  readPageByPageId(int $pageId)
 * @method IResponse                  readPagesByTitle(string $pageTitle, string $spaceKey = RequestParameterData::VAL_SPACE_EMPTY)
 * @method int                        checkPageExists(string $spaceKey, string $pageTitle, ItemTypeEnum $itemType = ItemTypeEnum::PAGE)
 * @method IResponse                  scanPages(string $spaceKey = RequestParameterData::VAL_SPACE_EMPTY)
 * @method IResponse                  searchPagesWithFilter(string $filterTerm, string $spaceKey, int $searchFromPos = IRapiClientBase::REQ_VAL_SEARCH_START, int $searchLimit = IRapiClientBase::REQ_VAL_SEARCH_LIMIT_MIN, ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE)
 * @method int                        spaceHomepage(string $spaceKey)
 *
 * <i>Statistic Methods</i>
 * @method IStatistic countItemsinSpace(string $spaceKey, ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE)
 * @method IStatistic countMacrosInSpace(string $spaceKey, ResponseAddonMacroDecorate $addonSet, IStatistic $outputMatrix)
 * @method IResponse  listSpaces(SpaceTypeEnum $spaceType = SpaceTypeEnum::SPACE_TYPE_GLOBAL, int $limit = IRapiClientBase::REQ_VAL_SPACE_LIMIT_DEFAULT)
 *
 * <i>Write Methods</i>
 * @method IResponse createPage(string $spaceKey, string $pageTitle, string $pageBody, int $parentId= IRapiClientBase::REQ_VAL_PARENT_ID_NO, ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE)
 * @method IResponse updatePage(int $pageId, string $pageBody, string $pageTitle = '', string $comment = '', ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE)
 * @method createOrUpdatePage(string $spaceKey, string $pageTitle, string $pageBody, int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO, ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE)
 * ): IResponse;
 * @method IResponse movePage(int $pageId, int $newParentId)
 *
 * <i>Restriction Methods</i>
 * @method IResponse readRestrictionsByPageId(int $pageId)
 * @method bool      writeRestrictionsByPageId(int $pageId, array<mixed,mixed> $writeRestriction, array<mixed,mixed> $readRestrictions)
 *
 * <i>Batch Methods</i>
 * @method int processQueue(ITaskList $taskList)
 */
class RapiClient extends AbstractRapiClient implements IRapiClient // NOSONAR: php:S1448
{
    use ExtensionTrait;
    use ClientReadTrait;
    use ClientWriteTrait;
    use ClientPermissionTrait;
    use ClientStatisticTrait;
    use ClientBatchTrait;

    private static LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function newClient(
        ?ExtensionEnum $modeExtension = self::EXTENSION_DEFAULT,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = self::LEVEL_DEFAULT
    ): IRapiClient {
        /** @psalm-suppress PossiblyInvalidArgument
         * @phpstan-ignore argument.type */
        return new RapiClient($modeExtension, $connectionProvider, $addons, $level);
    }

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
     * @param null|ExtensionEnum              $modeExtension      (Default: {@link self::EXTENSION_DEFAULT})
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|IContainer                 $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link self::LEVEL_DEFAULT})
     *
     * @see self::LEVEL_DEFAULT
     */
    protected function __construct(
        ?ExtensionEnum $modeExtension = self::EXTENSION_DEFAULT,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = self::LEVEL_DEFAULT
    ) {
        // Init Logger
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name: RapiClient::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($connectionProvider, $addons, $level);

        // Init Extensions
        if (is_null($modeExtension)) {
            $modeExtension = ExtensionEnum::EXTENSION_ALL;
        }
        $this->loadExtensions($modeExtension);

        self::$logger->debug('END');
    }
}
