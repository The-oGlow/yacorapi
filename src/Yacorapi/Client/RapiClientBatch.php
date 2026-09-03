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

use Monolog\ConsoleLogger;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\IConnectionProvider;
use ollily\Tools\Batch\ITaskItem;
use ollily\Tools\Batch\ITaskList;
use ollily\Tools\Batch\ProcessResultEnum;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings("PHPMD")
 */
class RapiClientBatch extends RapiClientStatistic implements IRapiClientStatistic
{
    private static LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param null|ExtensionEnum              $modeExtension      (Default: {@link IRapiClientBase::EXTENSION_DEFAULT})
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|IContainer                 $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link IRapiClientBase::LEVEL_DEFAULT})
     */
    protected function __construct(
        ?ExtensionEnum $modeExtension = IRapiClientBase::EXTENSION_DEFAULT,
        ?IConnectionProvider $connectionProvider = null,
        ?IContainer $addons = null,
        mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ) {
        /** @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name: RapiClientBatch::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($modeExtension, $connectionProvider, $addons, $level);

        self::$logger->debug('END');
    }

    public function processQueue(ITaskList $taskList): ProcessResultEnum
    {
        $processResult = ProcessResultEnum::FAIL;

        if ($taskList->isEmpty()) {
            $processResult = ProcessResultEnum::EMPTY;
        } else {
            $listConfig = $taskList->getListConfig();
            $listId = $taskList->getListId();
            while (!$taskList->isEmpty()) { // @phpstan-ignore booleanNot.alwaysTrue
                $taskResult = ProcessResultEnum::FAIL;
                /** @var ITaskItem $task */
                $task = $taskList->nextTask();
                if (!$task->empty()) {
                    $data = $task->getData();
                    $taskResult = $this->doSomething($task, $data, $listConfig);
                    if (ProcessResultEnum::SUCCESS != $taskResult) {
                        break;
                    }
                }
                if (ProcessResultEnum::SUCCESS == $taskResult) {
                    $processResult = ProcessResultEnum::SUCCESS;
                }
            }
        }

        return $processResult;
    }

    protected function doSomething(ITaskItem $task, mixed $data, mixed $listConfig): ProcessResultEnum
    {
        return ProcessResultEnum::SUCCESS;
    }
}
