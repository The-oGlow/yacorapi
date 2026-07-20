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
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Request\RequestType;
use oglow\tools\Yacorapi\Response\Response;

trait RapiWriteTrait
{
    /**
     * @inheritDoc
     */
    #[\Override]
    public function createPage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        int $parentId = self::REQ_NO_PARENT,
        string $itemType = self::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - spaceKey,pageTitle,parentId,pageType,pageBody', [$spaceKey, $pageTitle, $parentId, $itemType, empty($pageBody)]);

        /** @var Map<mixed,mixed> $parameters */
        $parameters = new Map(
            [
            RequestParameterData::PROP_TYPE => $itemType,
            RequestParameterData::PROP_TITLE => $pageTitle,
            RequestParameterData::PROP_STATUS => RequestParameterData::STATUS_TYPE_CURRENT,
            RequestParameterData::PROP_BODY => [
                RequestParameterData::PROP_STORAGE => [
                    RequestParameterData::PROP_VALUE => $pageBody,
                    RequestParameterData::PROP_REPRESENTATION => RequestParameterData::REPRESENTATION_TYPE_STORAGE,
                ],
            ],
            ]
        );
        if (empty($spaceKey)) {
            throw new \InvalidArgumentException(self::MSG_SPACE_IS_EMPTY);
        } else {
            $parameters->put(RequestParameterData::PROP_SPACE, [RequestParameterData::PROP_KEY => $spaceKey]);
        }
        if ($parentId > RequestParameterData::NO_PARENT) {
            $parameters->put(RequestParameterData::PROP_ANCESTORS, [[RequestParameterData::PROP_ID => $parentId]]);
        } else {
            throw new \InvalidArgumentException(self::MSG_PARENT_ID_MUST_BE_NUMERIC);
        }
        $prepareUrl = $this->commonExtension->prepareCreatePage();
        $response = $this->execPost($prepareUrl, $parameters, RequestType::REQ_TYP_POST);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function updatePage(
        int $pageId,
        string $pageBody,
        string $pageTitle = '',
        string $comment = '',
        string $itemType = self::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - pageId,pageTitle,pageType,bodySize,comment', [$pageId, $pageTitle, $itemType, strlen($pageBody), $comment]);

        $response = new Response();

        if (!empty($pageId)) {
            $currentPage = $this->readPageByPageId($pageId);
            $currentVersion = (int) $currentPage->getValue(RequestParameterData::PROP_VERSION, 1);
            $nextVersion = $currentVersion + 1;

            if (empty($comment)) {
                $comment = self::MSG_UPDATE_PAGE_WITHOUT_CHANGES;
            }
            $prepareURL = $this->commonExtension->prepareUpdateURL($pageId);
            $parameters = new Map(
                [
                RequestParameterData::PROP_ID => $pageId,
                RequestParameterData::PROP_TYPE => $itemType,
                RequestParameterData::PROP_TITLE => $pageTitle,
                RequestParameterData::PROP_BODY => [
                    RequestParameterData::PROP_STORAGE => [
                        RequestParameterData::PROP_VALUE => $pageBody,
                        RequestParameterData::PROP_REPRESENTATION => RequestParameterData::REPRESENTATION_TYPE_STORAGE,
                    ],
                ],
                RequestParameterData::PROP_VERSION => [RequestParameterData::PROP_NUMBER => $nextVersion, RequestParameterData::PROP_MESSAGE => $comment],
                ]
            );

            $response = $this->execPost($prepareURL, $parameters, RequestType::REQ_TYP_PUT);
            $success = $response->checkStatus();
            self::$logger->debug('Update page with title', [$pageId, $pageTitle, ($success ? 'successful' : 'failed')]);
        } else {
            self::$logger->error('No correct pageId', [$pageId]);
        }
        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function movePage(int $pageId, int $newParentId): IResponse
    {
        self::$logger->debug('START - pageId,newParentId', [$pageId, $newParentId]);

        $page = $this->readPageByPageId($pageId);

        $pageVersion = $page->getValue(RequestParameterData::PROP_VERSION, []);
        if (is_array($pageVersion) && array_key_exists(RequestParameterData::PROP_NUMBER, $pageVersion)) {
            $pageVersion = (int) $pageVersion[RequestParameterData::PROP_NUMBER];
        } else {
            $pageVersion = 1;
        }

        $parameters = new Map();
        $parameters->put(RequestParameterData::PROP_TITLE, $page->getValue(RequestParameterData::PROP_TITLE));
        $parameters->put(RequestParameterData::PROP_TYPE, $page->getValue(RequestParameterData::PROP_TYPE));
        $parameters->put(RequestParameterData::PROP_ANCESTORS, [[RequestParameterData::PROP_ID => $newParentId]]);
        $parameters->put(
            RequestParameterData::PROP_VERSION,
            [
                RequestParameterData::PROP_NUMBER => ++$pageVersion, // NOSONAR php:S881
                RequestParameterData::PROP_MESSAGE => self::MSG_MOVED_TO_NEW_PARENT . $newParentId,
            ]
        );

        $prepareUrl = $this->commonExtension->prepareUpdateURL($pageId);
        $response = $this->execPost($prepareUrl, $parameters, RequestType::REQ_TYP_PUT);

        self::$logger->debug('END');

        return $response;
    }
}
