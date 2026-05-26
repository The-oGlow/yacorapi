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

namespace oglow\tools\Yacorapi\Provider;

use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LogLevel;

class CurlProviderTest extends EasyGoingTestCase
{
    /**
     * @return CurlProvider
     */
    protected static function prepareO2t()
    {
        return new CurlProvider(null, LogLevel::DEBUG);
    }

    /**
     * @return CurlProvider
     */
    protected function getCasto2t()
    {
        return  $this->o2t;
    }
}
