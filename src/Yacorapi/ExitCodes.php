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

use Ds\Map;
use oglow\tools\common\AbstractSingleton;

class ExitCodes extends AbstractSingleton
{
    public const int ERR_CODE_NO_URL_SET               = 1;

    public const int ERR_CODE_AUTH_CLASS_NOT_EXISTS    = 12;

    public const int ERR_CODE_AUTHFILE_NOT_EXISTS      = 11;

    public const int ERR_CODE_EXTENSION_NOT_LOADED     = 13;

    public const int ERR_CODE_MYSPACES_FILE_NOT_EXISTS = 20;

    public const int ERR_CODE_BLOCKER_ADDON_NOT_INIT   = 26;

    public const int ERR_CODE_SINGLEADDON_NOT_INIT     = 25;

    public const int ERR_CODE_ALLADDON_NOT_INIT        = 27;

    public const int ERR_CODE_CURL_INIT = 28;

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareSettings(): void
    {
        // Nothing2Do
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function validateSettings(Map $overrideParameters): bool
    {
        return true;
    }
}
