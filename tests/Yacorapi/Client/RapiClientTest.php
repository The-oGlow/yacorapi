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

use oglow\tools\Yacorapi\IRapiClient;
use oglow\tools\Yacorapi\Provider\MockProvider;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LogLevel;

class RapiClientTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): IRapiClient
    {
        return RapiClient::newClient(connectionProvider: new MockProvider(LogLevel::DEBUG));
    }

    #[\Override]
    protected function getCasto2t(): IRapiClient
    {
        return $this->o2t;
    }
}
