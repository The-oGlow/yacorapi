<?php

/*
 * Copyright 2026 GLO03.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace oglow\tools\Yacorapi\Client;

use ollily\Tools\Batch\ITaskList;
use ollily\Tools\Batch\ITaskItem;
use ollily\Tools\Batch\ProcessResultEnum;

trait ClientBatchTrait {
    
    
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
