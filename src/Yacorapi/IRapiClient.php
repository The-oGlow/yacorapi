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

namespace oglow\tools\Yacorapi;

use Ds\Set;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\Client\IRapiClientBase;
use oglow\tools\Yacorapi\Client\IRapiClientBatch;
use oglow\tools\Yacorapi\Client\IRapiClientPermission;
use oglow\tools\Yacorapi\Client\IRapiClientRead;
use oglow\tools\Yacorapi\Client\IRapiClientStatistic;
use oglow\tools\Yacorapi\Client\IRapiClientWrite;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use Psr\Log\LogLevel;

interface IRapiClient extends IRapiClientRead, IRapiClientWrite, IRapiClientStatistic, IRapiClientPermission, IRapiClientBatch
{
    /**
     * Create new RapiClient.
     *
     * @param null|ExtensionEnum       $modeExtension      (Default: {@link IRapiClientBase::EXTENSION_DEFAULT})
     * @param null|IConnectionProvider $connectionProvider
     * @param null|IContainer          $addons
     * @param int|LogLevel|string      $level              (Default: {@link IRapiClientBase::LEVEL_DEFAULT})
     *
     * @return IRapiClient
     *
     * @see IRapiClient::LEVEL_DEFAULT
     * @see IRapiClient::EXTENSION_DEFAULT
     */
    public static function newClient(
        ?ExtensionEnum $modeExtension = IRapiClientBase::EXTENSION_DEFAULT,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ): IRapiClient;

    /**
     * @return Set<string> All available REST-API methods
     *
     * @phpstan-return Set<non-empty-string>
     */
    public static function taskitemMethods(): Set;
}
