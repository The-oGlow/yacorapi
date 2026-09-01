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
use Ds\Collection;
use InvalidArgumentException;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Request\RequestTypeEnum;
use oglow\tools\Yacorapi\Response\Response;

trait ClientWriteTrait
{
    public const string MSG_MOVED_TO_NEW_PARENT = 'Page moved to new parent';

    public const string MSG_PAGE_CREATED = 'Page created';

    public const string MSG_UPDATE_PAGE_WITH_CHANGES = 'Page content or title updated';

    private const string ERR_MSG_SPACE_IS_EMPTY = 'SpaceKey is empty';

    private const string ERR_MSG_PARENT_ID_MUST_BE_NUMERIC = 'ParentId must be numeric';

    private const string ERR_MSG_PAGE_ID_INVALID = 'No correct pageId';

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
        int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - spaceKey,pageTitle,parentId,pageType,pageBody', [$spaceKey, $pageTitle, $parentId, $itemType, empty($pageBody)]);

        $parameters = $this->prepareRequestParameter(
            mode: self::MODE_REQ_PARAM_CREATE,
            spaceKey: $spaceKey,
            pageTitle: $pageTitle,
            itemType: $itemType,
            parentId: $parentId,
            pageBody: $pageBody,
            nextVersion: IRapiClientBase::REQ_VAL_VERSION_FIRST
        );
        $prepareUrl = $this->prepareCreatePage();

        $response = $this->execPost($prepareUrl, $parameters, RequestTypeEnum::POST);

        $success = $response->checkStatus();
        self::$logger->debug('Created page with title', [$pageTitle, ($success ? 'successful' : 'failed')]);

        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     * 
     * @SuppressWarnings("PHPMD.UnusedLocalVariable")
     */
    #[\Override]
    public function updatePage(
        int $pageId,
        string $pageBody,
        string $pageTitle = '',
        string $comment = '',
        ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - pageId,pageTitle,pageType,bodySize,comment', [$pageId, $pageTitle, $itemType, strlen($pageBody), $comment]);

        $response = new Response();

        if (empty($pageId) || IRapiClientBase::REQ_VAL_PAGE_ID_NO == $pageId) {
            self::$logger->error(self::ERR_MSG_PAGE_ID_INVALID, [$pageId]);
        } else {
            [$currentVersion, $nextVersion, $currentPageTitle] = $this->nextVersionOfPage($pageId);
            if (empty($pageTitle)) {
                $pageTitle = $currentPageTitle;
            }

            $parameters = $this->prepareRequestParameter(
                mode: self::MODE_REQ_PARAM_UPDATE,
                pageTitle: $pageTitle,
                itemType: $itemType,
                pageBody: $pageBody,
                pageId: $pageId,
                nextVersion: $nextVersion,
                comment: $comment
            );
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
    public function createOrUpdatePage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE
    ): IResponse {
        $response = new Response();

        $itemPageId = $this->checkPageExists($spaceKey, $pageTitle);
        if ($itemPageId == IRapiClientBase::REQ_VAL_PAGE_ID_NO) {
            // Create page
            $response = $this->createPage($spaceKey, $pageTitle, $pageBody, $parentId);
        } else {
            // Update page
            $response = $this->updatePage($itemPageId, $pageBody, $pageTitle);
        }

        return $response;
    }

    /**
     * @inheritDoc
     * 
     * @SuppressWarnings("PHPMD.UnusedLocalVariable")
     */
    #[\Override]
    public function movePage(int $pageId, int $newParentId): IResponse
    {
        self::$logger->debug('START - pageId,newParentId', [$pageId, $newParentId]);

        $page = $this->readPageByPageId($pageId);

        [$currentVersion, $nextVersion, $pageTitle, $itemType] = $this->nextVersionOfPage($pageId);

        $parameters = $this->prepareRequestParameter(
            mode: self::MODE_REQ_PARAM_MOVE,
            pageTitle: $pageTitle,
            itemType: $itemType,
            newParentId: $newParentId,
            nextVersion: $nextVersion
        );
        $prepareUrl = $this->prepareUpdateURL($pageId);

        $response = $this->execPost($prepareUrl, $parameters, RequestTypeEnum::PUT);

        $success = $response->checkStatus();
        self::$logger->debug('Moved page with title/pageId to', [$pageTitle, $pageId, $newParentId, ($success ? 'successful' : 'failed')]);

        self::$logger->debug('END');

        return $response;
    }

    protected function prepareUpdateURL(int $pageId): string
    {
        return sprintf('%s/%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $pageId);
    }

    protected function prepareCreatePage(): string
    {
        return sprintf('%s/', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL));
    }

    /**
     * Retrieves the current version of the page and returns the next version.
     *
     * @param int $pageId Id of the page
     *
     * @return array<mixed,mixed> Returns [currentVersion,nextVersion,pageTitle,itemType]
     */
    protected function nextVersionOfPage(int $pageId): array
    {
        $currentVersion = IRapiClientBase::RESP_VAL_VERSION_NO;
        $nextVersion = IRapiClientBase::RESP_VAL_VERSION_NO;
        $pageTitle = IRapiClientBase::RESP_VAL_TITLE_EMPTY;
        $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE;

        $currentPage = $this->readPageByPageId($pageId);
        if ($currentPage->checkStatus()) {
            $pageTitle = $currentPage->getValue(IResponse::KEY_TITLE);
            $itemType = $currentPage->getValue(IResponse::KEY_TYPE);
            $itemType = ItemTypeEnum::tryFrom($itemType);
            $versionData = $currentPage->getValue(IResponse::KEY_VERSION, []);
            $currentVersion = array_key_exists(IResponse::KEY_NUMBER, $versionData) ? $versionData[IResponse::KEY_NUMBER] : IRapiClientBase::RESP_VAL_VERSION_NO;
            $nextVersion = $currentVersion + 1;
        } else {
            self::$logger->warning('Cannot find page', [$pageId]);
        }

        return [$currentVersion, $nextVersion, $pageTitle, $itemType];
    }

    /**
     * @param int          $mode
     * @param string       $pageTitle
     * @param ItemTypeEnum $itemType
     * @param string       $pageBody
     * @param int          $parentId
     * @param string       $spaceKey
     * @param int          $pageId
     * @param int          $newParentId
     * @param int          $nextVersion
     * @param string       $comment
     *
     * @return Map<mixed,mixed>
     *
     * @throws InvalidArgumentException
     * 
     * @SuppressWarnings("PHPMD.ExcessiveMethodLength")
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    protected function prepareRequestParameter(
        int $mode,
        string $pageTitle,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_ITEM_TYPE_PAGE,
        string $pageBody = IRapiClientBase::REQ_VAL_BODY_EMPTY,
        int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        string $spaceKey = IRapiClientBase::REQ_VAL_SPACE_EMPTY,
        int $pageId = IRapiClientBase::REQ_VAL_PAGE_ID_NO,
        int $newParentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        int $nextVersion = IRapiClientBase::RESP_VAL_VERSION_NO,
        string $comment = ''
    ): Collection {
        switch ($mode) {
            case self::MODE_REQ_PARAM_CREATE:
                if (empty($comment)) {
                    $comment = self::MSG_PAGE_CREATED;
                }

                /** @var Map<mixed,mixed> $parameters */
                $parameters = new Map(
                    [
                    RequestParameterData::PROP_TYPE => $itemType,
                    RequestParameterData::PROP_TITLE => $pageTitle,
                    RequestParameterData::PROP_STATUS => RequestParameterData::VAL_STATUS_TYPE_CURRENT,
                    RequestParameterData::PROP_BODY => [
                        RequestParameterData::PROP_STORAGE => [
                            RequestParameterData::PROP_VALUE => $pageBody,
                            RequestParameterData::PROP_REPRESENTATION => RequestParameterData::VAL_REPRESENTATION_TYPE_STORAGE,
                        ],
                    ],
                    RequestParameterData::PROP_VERSION => [
                        RequestParameterData::PROP_NUMBER => $nextVersion,
                        RequestParameterData::PROP_MESSAGE => $this->validateComment($comment),
                    ],
                        ]
                );
                if (empty($spaceKey)) {
                    throw new InvalidArgumentException(self::ERR_MSG_SPACE_IS_EMPTY);
                } else {
                    $parameters->put(RequestParameterData::PROP_SPACE, [RequestParameterData::PROP_KEY => $spaceKey]);
                }
                if ($parentId > IRapiClientBase::REQ_VAL_PARENT_ID_NO) {
                    $parameters->put(RequestParameterData::PROP_ANCESTORS, [[RequestParameterData::PROP_ID => $parentId]]);
                } else {
                    throw new InvalidArgumentException(self::ERR_MSG_PARENT_ID_MUST_BE_NUMERIC);
                }
                break;
            case self::MODE_REQ_PARAM_UPDATE:
                if (empty($comment)) {
                    $comment = self::MSG_UPDATE_PAGE_WITH_CHANGES;
                }

                /** @var Map<mixed,mixed> $parameters */
                $parameters = new Map(
                    [
                    RequestParameterData::PROP_ID => $pageId,
                    RequestParameterData::PROP_TYPE => $itemType,
                    RequestParameterData::PROP_TITLE => $pageTitle,
                    RequestParameterData::PROP_BODY => [
                        RequestParameterData::PROP_STORAGE => [
                            RequestParameterData::PROP_VALUE => $pageBody,
                            RequestParameterData::PROP_REPRESENTATION => RequestParameterData::VAL_REPRESENTATION_TYPE_STORAGE,
                        ],
                    ],
                    RequestParameterData::PROP_VERSION => [
                        RequestParameterData::PROP_NUMBER => $nextVersion,
                        RequestParameterData::PROP_MESSAGE => $this->validateComment($comment),
                    ],
                        ]
                );
                break;
            case self::MODE_REQ_PARAM_MOVE:
                if (empty($comment)) {
                    $comment = self::MSG_MOVED_TO_NEW_PARENT . $newParentId;
                }

                /** @var Map<mixed,mixed> $parameters */
                $parameters = new Map(
                    [
                    RequestParameterData::PROP_TITLE, $pageTitle,
                    RequestParameterData::PROP_TYPE, $itemType,
                    RequestParameterData::PROP_ANCESTORS, [
                        [
                            RequestParameterData::PROP_ID => $newParentId,
                        ],
                    ],
                    RequestParameterData::PROP_VERSION,
                    [
                        RequestParameterData::PROP_NUMBER => $nextVersion,
                        RequestParameterData::PROP_MESSAGE => $this->validateComment($comment),
                    ],
                        ]
                );
                break;
            default:
                /** @var Map<mixed,mixed> $parameters */
                $parameters = new Map();
                break;
        }

        return $parameters;
    }
}
