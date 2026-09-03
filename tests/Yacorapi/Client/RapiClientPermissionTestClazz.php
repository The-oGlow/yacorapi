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

class RapiClientPermissionTestClazz extends RapiClientPermission implements IRapiClientPermission
{
    protected ConstData $constData;

    private static LoggerInterface $logger;

    protected IConnectionProvider $connectionProvider;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(name: RapiClientPermissionTestClazz::class, level: self::LEVEL_DEFAULT);
        self::$logger->debug('START');

        parent::__construct(connectionProvider: new MockProvider());

        $this->constData = new ConstData(RapiClientPermissionTestClazz::class);
        $this->connectionProvider = new MockProvider();

        self::$logger->debug('END');
    }

    public function publicPrepareRestrictByOpUrl(int $pageId): string
    {
        return $this->prepareRestrictByOpUrl($pageId);
    }

    public function publicPrepareRestrictUpdateUrl(int $pageId): string
    {
        return $this->prepareRestrictUpdateUrl($pageId);
    }

    /**
     * @param int                $pageId
     * @param array<mixed,mixed> $writeRestrictions
     * @param array<mixed,mixed> $readRestrictions
     *
     * @return bool
     */
    public function publicWriteRestrictionsByPageId(int $pageId, array $writeRestrictions = [], array $readRestrictions = []): bool
    {
        return $this->writeRestrictionsByPageId($pageId, $writeRestrictions, $readRestrictions);
    }

    /**
     * @param array<mixed,mixed> $readRestrictions
     *
     * @return array<mixed,mixed>
     */
    public function publicAddRestrictionForGroup(array $readRestrictions): array
    {
        return $this->addRestrictionForGroup($readRestrictions);
    }

    /**
     * @param array<mixed,mixed> $readRestrictions
     *
     * @return array<mixed,mixed>
     */
    public function publicAddRestrictionForUser(array $readRestrictions): array
    {
        return $this->addRestrictionForUser($readRestrictions);
    }
}
