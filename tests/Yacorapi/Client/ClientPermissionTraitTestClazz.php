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
use oglow\tools\common\MockProvider;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\IConnectionProvider;
use Psr\Log\LoggerInterface;

class ClientPermissionTraitTestClazz implements IRapiClientPermission
{
    use ClientPermissionTrait;

    protected ConstData $constData;

    private static LoggerInterface $logger;

    protected IConnectionProvider $connectionProvider;

    public function __construct()
    {
        self::$logger    = new ConsoleLogger(ClientPermissionTraitTestClazz::class);
        $this->constData = new ConstData(ClientPermissionTraitTestClazz::class);
        $this->connectionProvider  = new MockProvider();
    }

    public function publicPrepareRestrictByOpUrl(int $pageId): string
    {
        return $this->prepareRestrictByOpUrl($pageId);
    }

    public function publicPrepareRestrictUpdateUrl(int $pageId): string
    {
        return $this->prepareRestrictUpdateUrl($pageId);
    }

    public function publicWriteRestrictionsByPageId(int $pageId, array $writeRestrictions = [], array $readRestrictions = []): bool
    {
        return $this->writeRestrictionsByPageId($pageId, $writeRestrictions, $readRestrictions);
    }

    public function publicAddRestrictionForGroup(array $readRestrictions): array
    {
        return $this->addRestrictionForGroup($readRestrictions);
    }

    public function publicAddRestrictionForUser(array $readRestrictions): array
    {
        return $this->addRestrictionForUser($readRestrictions);
    }
}
