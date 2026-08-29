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
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Request\RequestTypeEnum;
use oglow\tools\Yacorapi\Response\Response;
use InvalidArgumentException;

trait ClientWriteTrait {

    private const string MSG_MOVED_TO_NEW_PARENT = 'Page moved to new parent ';
    private const string MSG_PAGE_CREATED = 'Created the page';
    private const string MSG_UPDATE_PAGE_WITH_CHANGES = 'Update page content';
    private const string ERR_SPACE_IS_EMPTY = 'spaceKey is empty!';
    private const string ERR_PARENT_ID_MUST_BE_NUMERIC = 'parentId must be numeric!';
    private const string ERR_PAGE_ID_INVALID = 'No correct pageId';
    private const int MODE_REQ_PARAM_CREATE = 1;
    private const int MODE_REQ_PARAM_UPDATE = 2;
    private const int MODE_REQ_PARAM_MOVE = 4;

    /**
     * @inheritDoc
     */
    #[\Override]
    public function createPage(
            string $spaceKey,
            string $pageTitle,
            string $pageBody,
            int $parentId = IRapiClientBase::REQ_NO_PARENT,
            ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - spaceKey,pageTitle,parentId,pageType,pageBody', [$spaceKey, $pageTitle, $parentId, $itemType, empty($pageBody)]);

        $comment = self::MSG_PAGE_CREATED;
        $parameters = $this->prepareRequestParameter(mode: self::MODE_REQ_PARAM_CREATE, spaceKey: $spaceKey, pageTitle: $pageTitle, itemType: $itemType,
                parentId: $parentId, pageBody: $pageBody, comment: $comment);
        $prepareUrl = $this->prepareCreatePage();

        $response = $this->execPost($prepareUrl, $parameters, RequestTypeEnum::POST);

        $success = $response->checkStatus();
        self::$logger->debug('Created page with title', [$pageTitle, ($success ? 'successful' : 'failed')]);

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
            string $comment = self::MSG_UPDATE_PAGE_WITH_CHANGES,
            ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - pageId,pageTitle,pageType,bodySize,comment', [$pageId, $pageTitle, $itemType, strlen($pageBody), $comment]);

        $response = new Response();

        if (empty($pageId) || IRapiClientBase::REQ_NO_PAGE_ID == $pageId) {
            self::$logger->error(self::ERR_PAGE_ID_INVALID, [$pageId]);
        } else {
            [$currentVersion, $nextVersion] = $this->nextVersionOfPage($pageId);

            if (empty($comment)) {
                $comment = self::MSG_UPDATE_PAGE_WITH_CHANGES;
            }
            $parameters = $this->prepareRequestParameter(mode: self::MODE_REQ_PARAM_UPDATE, pageTitle: $pageTitle, itemType: $itemType, pageBody: $pageBody,
                    pageId: $pageId, nextVersion: $nextVersion, comment: $comment);
            $prepareURL = $this->prepareUpdateURL($pageId);

            $response = $this->execPost($prepareURL, $parameters, RequestTypeEnum::PUT);

            $success = $response->checkStatus();
            self::$logger->debug('Update page with title/pageId', [$pageTitle, $pageId, ($success ? 'successful' : 'failed')]);
        }
        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function movePage(int $pageId, int $newParentId): IResponse {
        self::$logger->debug('START - pageId,newParentId', [$pageId, $newParentId]);

        $page = $this->readPageByPageId($pageId);

        [$currentVersion, $nextVersion, $pageTitle, $itemType] = $this->nextVersionOfPage($pageId);

        $comment = self::MSG_MOVED_TO_NEW_PARENT . $newParentId;
        $parameters = $this->prepareRequestParameter(mode: self::MODE_REQ_PARAM_MOVE, pageTitle: $pageTitle, itemType: $itemType, newParentId: $newParentId,
                nextVersion: $nextVersion, comment: $comment);
        $prepareUrl = $this->prepareUpdateURL($pageId);

        $response = $this->execPost($prepareUrl, $parameters, RequestTypeEnum::PUT);

        $success = $response->checkStatus();
        self::$logger->debug('Moved page with title/pageId to', [$pageTitle, $pageId, $newParentId, ($success ? 'successful' : 'failed')]);

        self::$logger->debug('END');

        return $response;
    }

    protected function prepareUpdateURL(int $pageId): string {
        return sprintf('%s/%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $pageId);
    }

    protected function prepareCreatePage(): string {
        return sprintf('%s/', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL));
    }

    /**
     * Retrieves the current version of the page and returns the next version.
     * 
     * @param int $pageId Id of the page
     * @return array Returns [currentVersion,nextVersion]
     */
    protected function nextVersionOfPage(int $pageId): array {
        $currentVersion = IRapiClientBase::RESP_NO_VERSION;
        $nextVersion = IRapiClientBase::RESP_NO_VERSION;
        $pageTitle = IRapiClientBase::RESP_EMPTY_TITLE;
        $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE;
        
        $currentPage = $this->readPageByPageId($pageId);
        if ($currentPage->checkStatus()) {
            $pageTitle = $currentPage->getValue(IResponse::KEY_TITLE);
            $itemType = $currentPage->getValue(IResponse::KEY_TYPE);
            $itemType = ItemTypeEnum::tryFrom($itemType);
            $versionData = $currentPage->getValue(IResponse::KEY_VERSION, []);
            $currentVersion = array_key_exists(IResponse::KEY_NUMBER, $versionData) ? $versionData[IResponse::KEY_NUMBER] : IRapiClientBase::RESP_NO_VERSION;
            $nextVersion = $currentVersion + 1;
        } else {
            self::$logger->warning('Cannot find page', [$pageId]);
        }
        return [$currentVersion, $nextVersion, $pageTitle, $itemType];
    }

    protected function prepareRequestParameter(
            int $mode,
            string $pageTitle,
            ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE,
            string $pageBody = IRapiClientBase::REQ_EMPTY_BODY,
            int $parentId = IRapiClientBase::REQ_NO_PARENT,
            string $spaceKey = IRapiClientBase::REQ_NO_SPACE,
            int $pageId = IRapiClientBase::REQ_NO_PAGE_ID,
            int $newParentId = IRapiClientBase::REQ_NO_PARENT,
            int $nextVersion = IRapiClientBase::RESP_NO_VERSION,
            string $comment = self::MSG_UPDATE_PAGE_WITH_CHANGES
    ): Map {
        switch ($mode) {
            case self::MODE_REQ_PARAM_CREATE;
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
                    RequestParameterData::PROP_VERSION => [
                        RequestParameterData::PROP_NUMBER => $nextVersion,
                        RequestParameterData::PROP_MESSAGE => $comment
                    ],
                        ]
                );
                if (empty($spaceKey)) {
                    throw new InvalidArgumentException(self::ERR_SPACE_IS_EMPTY);
                } else {
                    $parameters->put(RequestParameterData::PROP_SPACE, [RequestParameterData::PROP_KEY => $spaceKey]);
                }
                if ($parentId > IRapiClientBase::REQ_NO_PARENT) {
                    $parameters->put(RequestParameterData::PROP_ANCESTORS, [[RequestParameterData::PROP_ID => $parentId]]);
                } else {
                    throw new InvalidArgumentException(self::ERR_PARENT_ID_MUST_BE_NUMERIC);
                }
                break;

            case self::MODE_REQ_PARAM_UPDATE :
                /** @var Map<mixed,mixed> $parameters */
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
                    RequestParameterData::PROP_VERSION => [
                        RequestParameterData::PROP_NUMBER => $nextVersion,
                        RequestParameterData::PROP_MESSAGE => $comment
                    ],
                        ]
                );
                break;
            case self::MODE_REQ_PARAM_MOVE :
                $parameters = new Map(
                        [
                    RequestParameterData::PROP_TITLE, $pageTitle,
                    RequestParameterData::PROP_TYPE, $itemType,
                    RequestParameterData::PROP_ANCESTORS, [
                        [
                            RequestParameterData::PROP_ID => $newParentId
                        ]
                    ],
                    RequestParameterData::PROP_VERSION,
                    [
                        RequestParameterData::PROP_NUMBER => $nextVersion,
                        RequestParameterData::PROP_MESSAGE => self::MSG_MOVED_TO_NEW_PARENT . $newParentId,
                    ]
                        ]
                );
                break;
        }
        return $parameters;
    }
}
