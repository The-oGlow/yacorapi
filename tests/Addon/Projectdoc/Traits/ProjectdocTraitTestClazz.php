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

namespace oglow\tools\Addon\Projectdoc\Traits;

use oglow\tools\common\MockProvider;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IResponse;

class ProjectdocTraitTestClazz
{
    use ProjectdocTrait;

    /** @var ConstData */
    protected $constData;

    /** @var IConnectionProvider */
    protected $provider;

    public function __construct()
    {
        $this->constData = new ConstData(ProjectdocTraitTestClazz::class);
        $this->provider  = new MockProvider();
    }

    protected function exec(string $execUrl): IResponse
    {
        return $this->provider->exec($execUrl);
    }
}
