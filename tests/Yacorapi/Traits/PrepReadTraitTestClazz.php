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

namespace oglow\tools\Yacorapi\Traits;

use oglow\tools\Yacorapi\ConstData;

class PrepReadTraitTestClazz
{
    use PrepReadTrait;

    protected ConstData $constData;

    public function __construct()
    {
        $this->constData = new ConstData(PrepReadTraitTestClazz::class);
    }
}
