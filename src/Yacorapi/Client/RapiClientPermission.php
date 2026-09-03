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
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\QueryExtensionEnum;
use oglow\tools\Yacorapi\Request\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Request\RequestTypeEnum;
use Psr\Log\LoggerInterface;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\common\IContainer;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\Client\IRapiClientBase;

class RapiClientPermission extends RapiClientWrite implements IRapiClientPermission
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
        self::$logger = new ConsoleLogger(name: RapiClientPermission::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($modeExtension, $connectionProvider, $addons, $level);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readRestrictionsByPageId(int $pageId): IResponse
    {
        self::$logger->debug('START - pageId', [$pageId]);

        $prepareUrl = $this->prepareRestrictByOpUrl($pageId);

        return $this->exec($prepareUrl);
    }

    protected function prepareRestrictByOpUrl(int $pageId): string
    {
        return sprintf(
            '%s/%s' . ConstData::C_RAPI_RESTRICTION_BYOP . '?%s',
            $this->constData->c(ConstData::KEY_CONF_CONTENT_URL),
            $pageId,
            QueryExtensionEnum::REQP_RESTRICTIONS_FULL->value
        );
    }

    protected function prepareRestrictUpdateUrl(int $pageId): string
    {
        return sprintf(
            '%s/%s' . ConstData::C_RAPI_RESTRICTION,
            $this->constData->c(ConstData::KEY_CONF_CONTENT_URL),
            $pageId
        );
    }

    /**
     * REFACTOR: API-Function doesn't work or description is wrong.
     *
     * @param int                $pageId
     * @param array<mixed,mixed> $writeRestrictions
     * @param array<mixed,mixed> $readRestrictions
     *
     * @return bool
     */
    #[\Override]
    public function writeRestrictionsByPageId(int $pageId, array $writeRestrictions = [], array $readRestrictions = []): bool // NOSONAR: php:S1172
    {
        $success = false;
        $prepareUrl = $this->prepareRestrictUpdateUrl($pageId);

        $prepareParameters = new Map();
        $prepareParametersRestrictions = ['restrictions' => []];
        $prefix = [
            'content' => [
                'expanded' => true,
                'idProperties' => new \stdClass(),
            ],
        ];

        if (!empty($writeRestrictions)) {
            $prepareParametersWrite = [
                'operation' => 'update',
                'restrictions' => $this->addRestrictionForUser($writeRestrictions),
            ];
            // 'lastModificationDate' => date('Y-m-d\TH:i:s\Z')

            $prepareParametersRestrictions['restrictions']['update'] = $prepareParametersWrite;
            self::$logger->info(print_r($prepareParametersWrite, true));
        }

        if (!empty($readRestrictions)) {
            $prepareParametersRead = [
                'operation' => 'read',
                'restrictions' => $this->addRestrictionForUser($readRestrictions),
            ];
            self::$logger->info('', [$prepareParametersRead]);
            $prepareParametersRestrictions['restrictions']['read'] = $prepareParametersRead;
            // 'lastModificationDate' => date('Y-m-d\TH:i:s\Z')
        }

        $prepareParameters->putAll($prefix);
        $prepareParameters->putAll($prepareParametersRestrictions);
        self::$logger->debug('prepareParameters', [json_encode($prepareParameters)]);
        $response = $this->connectionProvider->execPost($prepareUrl, $prepareParameters, RequestTypeEnum::PUT);

        /**
         * @psalm-suppress RedundantCondition
         * @phpstan-ignore empty.variable
         */
        if (!empty($response)) {
            $success = true;
        }

        return $success;
    }

    /**
     * @param array<mixed,mixed> $readRestrictions
     *
     * @return array<mixed,mixed>
     *
     * REFACTOR: Switch array to Map
     */
    protected function addRestrictionForUser(array $readRestrictions): array
    {
        self::$logger->debug('START - readRestrictions', [$readRestrictions]);

        $readUser = [];
        if (array_key_exists(RequestParameterData::PROP_USER, $readRestrictions)) {
            foreach ($readRestrictions[RequestParameterData::PROP_USER] as $readRestriction) {
                $readUser[] = [RequestParameterData::PROP_TYPE => RequestParameterData::VAL_USER_TYPE_KNOWN, RequestParameterData::PROP_USERNAME => $readRestriction];
            }
        }

        // REFACTOR: really an array as return?
        return [RequestParameterData::PROP_USER => $readUser];
    }

    /**
     * @param array<mixed,mixed> $readRestrictions
     *
     * @return array<mixed,mixed>
     *
     * REFACTOR: Switch array to Map
     */
    protected function addRestrictionForGroup(array $readRestrictions): array
    {
        self::$logger->debug('START - readRestrictions', [$readRestrictions]);

        $readGroup = [];
        if (array_key_exists(RequestParameterData::PROP_GROUP, $readRestrictions)) {
            foreach ($readRestrictions[RequestParameterData::PROP_GROUP] as $readRestriction) {
                $readGroup[] = [RequestParameterData::PROP_TYPE => RequestParameterData::VAL_USER_TYPE_KNOWN, RequestParameterData::PROP_USERNAME => $readRestriction];
            }
        }

        // REFACTOR: really an array as return?
        return [RequestParameterData::PROP_GROUP => $readGroup];
    }
}
