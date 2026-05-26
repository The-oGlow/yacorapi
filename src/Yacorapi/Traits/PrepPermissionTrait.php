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

namespace oglow\tools\Yacorapi\Traits;

use Ds\Map;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\Request\RequestType;

trait PrepPermissionTrait
{
    public function prepareRestrictByOpUrl(int $pageId): string
    {
        return sprintf(
            '%s/%s' . ConstData::C_RAPI_RESTRICTION_BYOP . '?%s',
            $this->constData->c(ConstData::KEY_CONF_CONTENT_URL),
            $pageId,
            RequestParameterData::REQP_RESTRICTIONS_FULL
        );
    }

    public function prepareRestrictUpdateUrl(int $pageId): string
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
     * @param int     $pageId
     * @param mixed[] $writeRestrictions
     * @param mixed[] $readRestrictions
     *
     * @return bool
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function writeRestrictionsByPageId(int $pageId, array $writeRestrictions = [], array $readRestrictions = []): bool // NOSONAR: php:S1172
    {
        $success    = false;
        $prepareUrl = $this->prepareRestrictUpdateUrl($pageId);

        $prepareParameters             =  new Map();
        $prepareParametersRestrictions = ['restrictions' => []];
        $prefix                        = [
            'content' => [
                'expanded'     => true,
                'idProperties' => new \stdClass()
            ],
        ];

        if (!empty($writeRestrictions)) {
            $prepareParametersWrite = [
                'operation'    => 'update',
                'restrictions' => $this->addRestrictionForUser($writeRestrictions)
            ];
            // 'lastModificationDate' => date('Y-m-d\TH:i:s\Z')

            $prepareParametersRestrictions['restrictions']['update'] = $prepareParametersWrite;
            self::$logger->info(print_r($prepareParametersWrite, true));
        }

        if (!empty($readRestrictions)) {
            $prepareParametersRead = [
                'operation'    => 'read',
                'restrictions' => $this->addRestrictionForUser($readRestrictions)
            ];
            self::$logger->info('', [$prepareParametersRead]);
            $prepareParametersRestrictions['restrictions']['read'] = $prepareParametersRead;
            // 'lastModificationDate' => date('Y-m-d\TH:i:s\Z')
        }

        $prepareParameters->putAll($prefix);
        $prepareParameters->putAll($prepareParametersRestrictions);
        self::$logger->debug('prepareParameters', [json_encode($prepareParameters)]);
        $response = $this->connectionProvider->execPost($prepareUrl, $prepareParameters, RequestType::REQ_TYP_PUT);

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
     * @param mixed[] $readRestrictions
     *
     * @return mixed[]
     *
     * REFACTOR: Switch array to Map
     */
    public function addRestrictionForUser(array $readRestrictions): array
    {
        self::$logger->debug('START - readRestrictions', [$readRestrictions]);

        $readUser = [];
        if (array_key_exists(RequestParameterData::PROP_USER, $readRestrictions)) {
            foreach ($readRestrictions[RequestParameterData::PROP_USER] as $readRestriction) {
                $readUser[] =
                    [RequestParameterData::PROP_TYPE => RequestParameterData::USER_TYPE_KNOWN, RequestParameterData::PROP_USERNAME => $readRestriction];
            }
        }

        // REFACTOR: really an array as return?
        return [RequestParameterData::PROP_USER => $readUser];
    }

    /**
     * @param mixed[] $readRestrictions
     *
     * @return mixed[]
     *
     * REFACTOR: Switch array to Map
     */
    public function addRestrictionForGroup(array $readRestrictions): array
    {
        self::$logger->debug('START - readRestrictions', [$readRestrictions]);

        $readGroup = [];
        if (array_key_exists(RequestParameterData::PROP_GROUP, $readRestrictions)) {
            foreach ($readRestrictions[RequestParameterData::PROP_GROUP] as $readRestriction) {
                $readGroup[] =
                    [RequestParameterData::PROP_TYPE => RequestParameterData::USER_TYPE_KNOWN, RequestParameterData::PROP_USERNAME => $readRestriction];
            }
        }

        // REFACTOR: really an array as return?
        return [RequestParameterData::PROP_GROUP => $readGroup];
    }
}
