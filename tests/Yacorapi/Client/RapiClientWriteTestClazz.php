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
use Psr\Log\LoggerInterface;

class RapiClientWriteTestClazz extends RapiClientWrite implements  IRapiClientWrite
{

    private static LoggerInterface $logger;

    protected ConstData $constData;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(name: RapiClientWriteTestClazz::class, level: self::LEVEL_DEFAULT);
        self::$logger->debug('START');

        parent::__construct(connectionProvider: new MockProvider());

        $this->constData = new ConstData(RapiClientWriteTestClazz::class);

        self::$logger->debug('END');
    }

    public function publicPrepareUpdateURL(int $pageId): string
    {
        return $this->prepareUpdateURL($pageId);
    }

    public function publicPrepareCreatePage(): string
    {
        return $this->prepareCreatePage();
    }
}
