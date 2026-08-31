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
use oglow\tools\Yacorapi\Request\RequestTypeEnum;

interface IConnectionProvider
{
    public const string MSG_FOUND_NO_RESULTS = 'Found no results';

    public const string MSG_NOT_IMPLEMENTED  = 'Not implemented so far';

    /**
     * @param string          $execUrl
     * @param RequestTypeEnum $reqType
     *
     * @return IResponse
     */
    public function exec(string $execUrl, RequestTypeEnum $reqType = RequestTypeEnum::GET): IResponse;

    /**
     * @param string            $execUrl
     * @param Map<mixed, mixed> $parameters
     * @param RequestTypeEnum   $reqType
     *
     * @return IResponse
     */
    public function execPost(string $execUrl, Map $parameters, RequestTypeEnum $reqType): IResponse;
}
