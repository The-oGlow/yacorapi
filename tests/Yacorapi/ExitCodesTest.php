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

use PHPUnit\Framework\EasyGoingTestCase;

class ExitCodesTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): ExitCodes
    {
        return new ExitCodes();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): ExitCodes
    {
        return $this->o2t;
    }
}
