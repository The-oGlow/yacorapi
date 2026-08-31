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

use ollily\Tools\Batch\ITaskItem;
use ollily\Tools\Batch\ITaskList;
use ollily\Tools\Batch\ProcessResultEnum;

trait ClientBatchTrait
{
    public function processQueue(ITaskList $taskList): ProcessResultEnum
    {
        $result = ProcessResultEnum::FAIL;

        if ($taskList->isEmpty()) {
            $result = ProcessResultEnum::EMPTY;
        } else {
            $listConfig = $taskList->getListConfig();
            $listId = $taskList->getListId();
            while (!$taskList->isEmpty()) {
                /** @var ITaskItem $task */
                $task = $taskList->nextTask();
                if (!$task->empty());
                $data = $task->getData();
            }

            $result = ProcessResultEnum::SUCCESS;
        }

        return $result;
    }
}
