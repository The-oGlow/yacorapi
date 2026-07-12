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

use oglow\tools\Yacorapi\Response\ResponseDryRun;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LogLevel;

class CurlProviderTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): CurlProvider
    {
        return new CurlProvider(new ResponseDryRun(), LogLevel::DEBUG);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): CurlProvider
    {
        return  $this->o2t;
    }
}
