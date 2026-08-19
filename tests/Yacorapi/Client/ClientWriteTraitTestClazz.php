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

use oglow\tools\Yacorapi\ConstData;

class ClientWriteTraitTestClazz implements IRapiClientWrite
{
    use ClientWriteTrait;

    protected ConstData $constData;

    public function __construct()
    {
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
