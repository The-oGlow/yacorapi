<?php

/*
 * Copyright 2026 GLO03.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace oglow\tools\Yacorapi\Client;

use PHPUnit\Framework\EasyGoingTestCase;
use oglow\tools\Yacorapi\IRapiClient;
use Psr\Log\LoggerInterface;
use Monolog\ConsoleLogger;
use oglow\tools\common\MockProvider;
use Psr\Log\LogLevel;

class RapiClientTest extends EasyGoingTestCase {
    
    private static LoggerInterface $logger;

    #[\Override]
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        self::$logger = new ConsoleLogger(RapiClientTest::class);
    }

    #[\Override]
    protected static function prepareO2t(): IRapiClient {
        return RapiClient::newClient(connectionProvider: new MockProvider(LogLevel::DEBUG));
    }

    #[\Override]
    protected function getCasto2t(): IRapiClient {
        return $this->o2t;
    }
}
