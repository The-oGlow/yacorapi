<?php

/*
 * Copyright 2026 postm.
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

namespace oglow\tools\Yacorapi\Helper;

use Ds\Vector;
use Ds\Sequence;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\Helper\AbstractHelper;
use Psr\Log\LoggerInterface;
use DOMDocument;
use DOMXPath;
use DOMNodeList;

/**
 * Helper clazz for editing tags of a confluence page.
 * 
 * @author ollily
 */
class TagHelper extends AbstractHelper
{

    private static LoggerInterface $logger;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(TagHelper::class, level: static::LEVEL_DEFAULT);
        self::$logger->debug('START');

        parent::__construct(TagHelper::class);

        self::$logger->debug('END');
    }

    /**
     * @param string $tagName
     * @param DOMDocument $page
     * @return Sequence
     */
    public static function findTag(string $tagName, DOMDocument $page): Sequence
    {
        /** @var bool|DOMNodeList */
        $result = false;
        if (!empty($tagName)) {
            try {
                $xpath = new DOMXPath($page);
                $result = $xpath->query($tagName);
            } catch (\Throwable $exception) {
                self::$logger->notice($exception->getMessage());
            }
        }
        $tags = new Vector();
        if ($result) {
            $tags = new Vector($result);
        }
        return $tags;
    }
}
