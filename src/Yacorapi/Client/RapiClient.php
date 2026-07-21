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

use Monolog\ConsoleLogger;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use Psr\Log\LoggerInterface;

class RapiClient extends AbstractRapiClient // NOSONAR: php:S1448
{
    use RapiStatisticTrait;
    use RapiRestrictionTrait;
    use RapiWriteTrait;

    private static LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function newClient(
        ?int $modeExtension = null,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = self::LEVEL_DEFAULT
    ): IRapiClient {
        /** @psalm-suppress PossiblyInvalidArgument
         * @phpstan-ignore argument.type */
        return new RapiClient($modeExtension, $connectionProvider, $addons, $level);
    }

    /**
     * RapiClient constructor.
     *
     * @param null|int                        $modeExtension
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|IContainer                 $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link self::LEVEL_DEFAULT})
     *
     * @see self::LEVEL_DEFAULT
     */
    protected function __construct(
        ?int $modeExtension = null,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = self::LEVEL_DEFAULT
    ) {
        // Init Logger
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name: RapiClient::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($modeExtension, $connectionProvider, $addons, $level);

        self::$logger->debug('END');
    }

    // Public methods

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readPageByPageId(int $pageId): IResponse
    {
        self::$logger->debug('START - pageId', [$pageId]);

        $prepareUrl = $this->commonExtension->prepareLoadUrl($pageId);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse
    {
        self::$logger->debug('START - filterTerm,spaceKey', [$filterTerm, $spaceKey]);

        $prepareUrl = $this->commonExtension->prepareBrowseUrl($filterTerm, $spaceKey);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function scanPagesWithFilter(string $filterTerm, string $spaceKey = ''): IResponse
    {
        self::$logger->debug('START - filterTerm,spaceKey', [$filterTerm, $spaceKey]);

        $prepareUrl = $this->commonExtension->prepareScanUrl($filterTerm, $spaceKey);

        return $this->exec($prepareUrl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function searchPagesWithFilter(
        string $filterTerm,
        string $spaceKey,
        int $searchFromPos = self::REQ_SEARCH_FROM_POS,
        int $searchLimit = self::REQ_SEARCH_LIMIT,
        ItemTypeEnum $itemType = IRapiClient::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug(
            'START - filterTerm,spaceKey,searchFromPos,searchLimit,itemType',
            [$filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType]
        );
        $searchLimit = (int) ($searchLimit < self::REQ_SEARCH_LIMIT_1ENTRY ? $this->constData->c(ConstData::KEY_SEARCH_LIMIT) : $searchLimit);
        $prepareUrl = $this->commonExtension->prepareSearchUrlExt($filterTerm, $spaceKey, $searchFromPos, $searchLimit, $itemType);

        return $this->exec($prepareUrl);
    }

    // Private methods
}
