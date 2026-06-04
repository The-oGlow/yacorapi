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
use oglow\tools\Yacorapi\Request\RequestType;

interface IConnectionProvider
{
    public const string MSG_FOUND_NO_RESULTS = 'Found no results!';

    public const string MSG_NOT_IMPLEMENTED  = 'Not implemented so far!';

    /**
     * @param string $execUrl
     * @param int    $reqType
     *
     * @return IResponse
     */
    public function exec(string $execUrl, int $reqType = RequestType::REQ_TYP_GET): IResponse;

    /**
     * @param string            $execUrl
     * @param Map<mixed, mixed> $parameters
     * @param int               $reqType
     *
     * @return IResponse
     */
    public function execPost(string $execUrl, Map $parameters, int $reqType): IResponse;
}
