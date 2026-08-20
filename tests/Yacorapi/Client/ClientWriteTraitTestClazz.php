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

class ClientWriteTraitTestClazz extends AbstractRapiClient implements IRapiClientRead, IRapiClientWrite
{
    use ClientReadTrait;
    use ClientWriteTrait;

    private static LoggerInterface $logger;

    protected ConstData $constData;

    public function __construct()
    {
        parent::__construct(new MockProvider());
        self::$logger = new ConsoleLogger(ClientReadTraitTestClazz::class);
        $this->constData = new ConstData(ClientWriteTraitTestClazz::class);
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
