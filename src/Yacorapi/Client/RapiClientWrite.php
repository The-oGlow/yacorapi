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
use Ds\Map;
use InvalidArgumentException;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Request\RequestParameterData;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Request\RequestTypeEnum;
use oglow\tools\Yacorapi\Response\Response;
use Psr\Log\LoggerInterface;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\common\IContainer;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\Client\IRapiClientBase;
use Monolog\AbstractEasyGoingLogger;

/**
 * @phpstan-type PageInfoParam 'body'|'current'|'next'|'title'|'type'
 * @phpstan-type PageInfo array<PageInfoParam,mixed>
 */
class RapiClientWrite extends RapiClientRead implements IRapiClientWrite
{
    public const string MSG_MOVED_TO_NEW_PARENT = 'Page moved to new parent pageId';

    public const string MSG_PAGE_CREATED = 'Page created';

    public const string MSG_UPDATE_PAGE_WITH_CHANGES = 'Page content or title updated';

    private const string ERR_MSG_SPACE_IS_EMPTY = 'SpaceKey must not be empty';

    private const string ERR_MSG_PARENT_ID_MUST_BE_NUMERIC = 'ParentId must be numeric';

    private const string ERR_MSG_PAGE_ID_INVALID = 'No correct pageId';

    private const string ERR_MSG_PAGE_TITLE_EMPTY = 'Page title must no be empty';

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
        self::$logger = new ConsoleLogger(name: RapiClientWrite::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($modeExtension, $connectionProvider, $addons, $level);

        self::$logger->debug('END');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function createPage(
        string $spaceKey,
        string $pageTitle,
        string $pageBody,
        int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - spaceKey,pageTitle,parentId,itemType,len(pageBody),len(comment)');
        self::$logger->debug('', [$spaceKey, $pageTitle, $parentId, $itemType, strlen($pageBody), strlen($comment)]);

        $parameters = $this->prepareParameterCreateRequest(
            spaceKey: $spaceKey,
            pageTitle: $pageTitle,
            itemType: $itemType,
            parentId: $parentId,
            pageBody: $pageBody,
            nextVersion: IRapiClientBase::REQ_VAL_VERSION_FIRST,
            comment:  $comment
        );
        $response = $this->execPost($this->prepareCreatePage(), $parameters, RequestTypeEnum::POST);

        self::$logger->debug('Created page with title', [$pageTitle, $response->checkStatus()]);
        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function updatePage(
        int $pageId,
        string $pageBody = IRapiClientBase::REQ_VAL_BODY_EMPTY,
        string $pageTitle = IRapiClientBase::REQ_VAL_PAGE_TITLE_EMPTY,
        string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - pageId,pageTitle,itemType,len(pageBody),len(comment)');
        self::$logger->debug('', [$pageId, $pageTitle, $itemType, strlen($pageBody), strlen($comment)]);

        $response = new Response();

        if (empty($pageId) || IRapiClientBase::REQ_VAL_PAGE_ID_NO == $pageId) {
            self::$logger->error(self::ERR_MSG_PAGE_ID_INVALID, [$pageId]);
        } else {
            $pageInfo = $this->loadItemInfo($pageId);
            if (empty($pageTitle)) {
                $pageTitle = $pageInfo['title'];
            }
            if (empty($pageBody)) {
                $pageBody = $pageInfo['body'];
            }

            $parameters = $this->prepareParameterUpdateRequest(
                pageTitle: $pageTitle,
                itemType: $itemType,
                pageBody: $pageBody,
                pageId: $pageId,
                nextVersion: $pageInfo['next'],
                comment: $comment
            );
            $response = $this->execPost($this->prepareUpdateURL($pageId), $parameters, RequestTypeEnum::PUT);

            self::$logger->debug('Updated page with title/pageId', [$pageTitle, $pageId, $response->checkStatus()]);
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
        string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE
    ): IResponse {
        self::$logger->debug('START - spaceKey,pageTitle,itemType,parentId,len(pageBody),len(comment)');
        self::$logger->debug('', [$spaceKey, $pageTitle, $itemType, $parentId, strlen($pageBody), strlen($comment)]);

        $response = new Response();

        $itemPageId = $this->checkPageExists($spaceKey, $pageTitle);
        if ($itemPageId == IRapiClientBase::REQ_VAL_PAGE_ID_NO) {
            // Create page
            $response = $this->createPage($spaceKey, $pageTitle, $pageBody, $parentId, $comment);
        } else {
            // Update page
            $response = $this->updatePage($itemPageId, $pageBody, $pageTitle, $comment);
        }

        self::$logger->debug('Created/Updated page with title', [$pageTitle, $response->checkStatus()]);
        self::$logger->debug('END');

        return $response;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function movePage(int $pageId, int $newParentId, string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY): IResponse
    {
        self::$logger->debug('START - pageId,newParentId,len(comment)');
        self::$logger->debug('', [$pageId, $newParentId, strlen($comment)]);

        $pageInfo = $this->loadItemInfo($pageId);

        $parameters = $this->prepareParameterMoveRequest(
            pageTitle: $pageInfo['title'],
            itemType: $pageInfo['type'],
            newParentId: $newParentId,
            nextVersion: $pageInfo['next'],
            comment: $comment
        );
        $response = $this->execPost($this->prepareUpdateURL($pageId), $parameters, RequestTypeEnum::PUT);

        self::$logger->debug('Moved page with title/pageId to', [$pageInfo['title'], $pageId, $newParentId, $response->checkStatus()]);
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
     *
     * @phpstan-return PageInfo
     */
    protected function loadItemInfo(int $pageId): array
    {
        $currentVersion = IRapiClientBase::RESP_VAL_VERSION_NO;
        $nextVersion = IRapiClientBase::RESP_VAL_VERSION_NO;
        $pageTitle = IRapiClientBase::RESP_VAL_TITLE_EMPTY;
        $pageBody = IRapiClientBase::RESP_VAL_BODY_EMPTY;
        $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE;

        $currentPage = $this->readPageByPageId($pageId);
        if ($currentPage->checkStatus()) {
            $pageTitle = $currentPage->getValue(IResponse::KEY_TITLE);
            $pageBody =  $currentPage->getValue(IResponse::KEY_BODY)[IResponse::KEY_STORAGE][IResponse::KEY_VALUE];
            $itemType = ItemTypeEnum::tryFrom($currentPage->getValue(IResponse::KEY_TYPE));
            $versionData = $currentPage->getValue(IResponse::KEY_VERSION, []);
            $currentVersion = intval(array_key_exists(IResponse::KEY_NUMBER, $versionData) ? $versionData[IResponse::KEY_NUMBER] : IRapiClientBase::RESP_VAL_VERSION_NO);
            $nextVersion = $currentVersion + 1;
        } else {
            self::$logger->warning('Cannot find page', [$pageId]);
        }

        return ['current' => $currentVersion, 'next' => $nextVersion, 'title' => $pageTitle, 'type' => $itemType, 'body' => $pageBody];
    }

    /**
     * @param string       $pageTitle
     * @param ItemTypeEnum $itemType
     * @param string       $pageBody
     * @param int          $parentId
     * @param string       $spaceKey
     * @param int          $nextVersion
     * @param string       $comment
     *
     * @return Collection<mixed,mixed>
     *
     * @throws InvalidArgumentException
     */
    protected function prepareParameterCreateRequest(
        string $pageTitle,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE,
        string $pageBody = IRapiClientBase::REQ_VAL_BODY_EMPTY,
        int $parentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        string $spaceKey = IRapiClientBase::REQ_VAL_SPACE_EMPTY,
        int $nextVersion = IRapiClientBase::RESP_VAL_VERSION_NO,
        string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY
    ): Collection {
        if (empty($comment)) {
            $comment = self::MSG_PAGE_CREATED;
        }

        if (empty($pageTitle)) {
            throw new InvalidArgumentException(self::ERR_MSG_PAGE_TITLE_EMPTY);
        }

        if (empty($spaceKey)) {
            throw new InvalidArgumentException(self::ERR_MSG_SPACE_IS_EMPTY);
        }

        /** @var Map<mixed,mixed> */
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
            RequestParameterData::PROP_SPACE => [RequestParameterData::PROP_KEY => $spaceKey],
            RequestParameterData::PROP_VERSION => [
                RequestParameterData::PROP_NUMBER => $nextVersion,
                RequestParameterData::PROP_MESSAGE => $this->validateComment($comment),
            ],
                ]
        );
        if ($parentId > IRapiClientBase::REQ_VAL_PARENT_ID_NO) {
            $parameters->put(RequestParameterData::PROP_ANCESTORS, [[RequestParameterData::PROP_ID => $parentId]]);
        } else {
            throw new InvalidArgumentException(self::ERR_MSG_PARENT_ID_MUST_BE_NUMERIC);
        }

        self::$logger->debug('parameters', [$parameters]);

        return $parameters;
    }

    /**
     * @param string       $pageTitle
     * @param ItemTypeEnum $itemType
     * @param string       $pageBody
     * @param int          $pageId
     * @param int          $nextVersion
     * @param string       $comment
     *
     * @return Collection<mixed,mixed>
     *
     * @throws InvalidArgumentException
     */
    protected function prepareParameterUpdateRequest(
        string $pageTitle,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE,
        string $pageBody = IRapiClientBase::REQ_VAL_BODY_EMPTY,
        int $pageId = IRapiClientBase::REQ_VAL_PAGE_ID_NO,
        int $nextVersion = IRapiClientBase::RESP_VAL_VERSION_NO,
        string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY
    ): Collection {
        if (empty($comment)) {
            $comment = self::MSG_UPDATE_PAGE_WITH_CHANGES;
        }

        if (IRapiClientBase::REQ_VAL_PAGE_ID_NO == $pageId) {
            throw new InvalidArgumentException(self::ERR_MSG_PAGE_ID_INVALID);
        }

        if (empty($pageTitle)) {
            throw new InvalidArgumentException(self::ERR_MSG_PAGE_TITLE_EMPTY);
        }

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
        self::$logger->debug('parameters', [$parameters]);

        return $parameters;
    }

    /**
     * @param string       $pageTitle
     * @param ItemTypeEnum $itemType
     * @param int          $newParentId
     * @param int          $nextVersion
     * @param string       $comment
     *
     * @return Collection<mixed,mixed>
     *
     * @throws InvalidArgumentException
     */
    protected function prepareParameterMoveRequest(
        string $pageTitle,
        ItemTypeEnum $itemType = IRapiClientBase::REQ_VAL_ITEM_TYPE_PAGE,
        int $newParentId = IRapiClientBase::REQ_VAL_PARENT_ID_NO,
        int $nextVersion = IRapiClientBase::RESP_VAL_VERSION_NO,
        string $comment = IRapiClientBase::REQ_VAL_COMMENT_EMPTY
    ): Collection {
        if (empty($comment)) {
            $comment = sprintf('%s %s', self::MSG_MOVED_TO_NEW_PARENT, $newParentId);
        }

        if (IRapiClientBase::REQ_VAL_PARENT_ID_NO == $newParentId) {
            throw new InvalidArgumentException(self::ERR_MSG_PARENT_ID_MUST_BE_NUMERIC);
        }

        if (empty($pageTitle)) {
            throw new InvalidArgumentException(self::ERR_MSG_PAGE_TITLE_EMPTY);
        }

        $parameters = new Map(
            [
            RequestParameterData::PROP_TYPE => $itemType,
            RequestParameterData::PROP_TITLE => $pageTitle,
            RequestParameterData::PROP_ANCESTORS => [[RequestParameterData::PROP_ID => $newParentId]],
            RequestParameterData::PROP_VERSION =>
            [
                RequestParameterData::PROP_NUMBER => $nextVersion,
                RequestParameterData::PROP_MESSAGE => $this->validateComment($comment),
            ],
                ]
        );
        self::$logger->debug('parameters', [$parameters]);

        return $parameters;
    }
}
