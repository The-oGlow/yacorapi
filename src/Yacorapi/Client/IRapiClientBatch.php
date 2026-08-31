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

use ollily\Tools\Batch\ITaskList;
use ollily\Tools\Batch\ProcessResultEnum;

interface IRapiClientBatch extends IRapiClientBase
{
    /**
     * The items of the  tasklist will be done.
     *
     * @param ITaskList $taskList
     *
     * @return ProcessResultEnum
     */
    public function processQueue(ITaskList $taskList): ProcessResultEnum;
}
