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

namespace oglow\tools\Yacorapi\Extension;

use oglow\tools\Yacorapi\ConstData;

trait PrepWriteTrait
{
    public function prepareUpdateURL(int $pageId): string
    {
        return sprintf('%s/%s', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL), $pageId);
    }

    public function prepareCreatePage(): string
    {
        return sprintf('%s/', $this->constData->c(ConstData::KEY_CONF_CONTENT_URL));
    }
}
