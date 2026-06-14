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

namespace oglow\tools\Addon\Projectdoc\Helper;

use Ds\Map;
use Monolog\ConsoleLogger;
use Monolog\DoNothingLogger;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Helper\AbstractHelper;
use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Store\FileAdapter;
use oglow\tools\Yacorapi\Store\IStoreAdapter;
use Psr\Log\LoggerInterface;

class ProjectDocToolboxHelper extends AbstractHelper
{
    private static LoggerInterface $logger;

    public function __construct(bool $withLogger = true)
    {
        if ($withLogger) {
            self::$logger = new ConsoleLogger(ProjectDocToolboxHelper::class);
        } else {
            self::$logger = new DoNothingLogger();
        }
        self::$logger->debug('START');

        parent::__construct(ProjectDocToolboxHelper::class, $withLogger);

        self::$logger->debug('END');
    }

    private function prepareStoreAdapter(string $fileName): IStoreAdapter
    {
        return new FileAdapter($fileName, 'html', $this->constData->c(ConstData::KEY_TARGET_DIR));
    }

    /**
     * @param string $body
     * @param string $oldDoctype
     * @param string $newDoctype
     *
     * @return null|string
     */
    public function replaceDoctype(string $body, string $oldDoctype, string $newDoctype): ?string
    {
        $newBody = $body;
        if (!empty($oldDoctype)) {
            $pattern =
            "/(ac:name=.projectdoc-properties-marker.\sac:schema-version=.1.><ac:parameter\sac:name=.doctype.>)(" . $oldDoctype . ")(<\/ac:parameter>)/";
            self::$logger->debug('Search pattern', [$oldDoctype,$newDoctype,$pattern]);
            $newBody = preg_replace($pattern, "$1" . $newDoctype . "$3", $body);
        }
        if ($body == $newBody) {
            self::$logger->notice('Doctype not replaced', [$oldDoctype, $newDoctype]);
        }

        return $newBody;
    }

    public function replaceAndStoreDoctype(string $fileName, string $oldBody, string $oldDoctype, string $newDoctype): bool
    {
        $stored  = false;
        $newBody = $this->replaceDoctype($oldBody, $oldDoctype, $newDoctype);
        if ($oldBody !== $newBody) {
            $storeAdapter = $this->prepareStoreAdapter($fileName);
            $storeAdapter->storeData($newBody);
            $stored = true;
        } else {
            self::$logger->notice('Doctype not changed & stored', [$oldDoctype, $newDoctype]);
        }

        return $stored;
    }

    public function modifyData(?IResponse $response, string $oldDoctype, string $newDoctype): bool
    {
        $modified = false;
        if (!empty($response) && $response->isResultsAvailable()) {
            $idx     = 0;
            $results = $response->getResults();
            foreach ($results as $page) {
                $pageId    = $page[IResponse::KEY_KEY];
                $pageTitle = $page[IResponse::KEY_TITLE];
                $pageBody  = $page[IResponse::KEY_BODY][IResponse::KEY_STORAGE][IResponse::KEY_VALUE];
                self::$logger->debug(sprintf("%s. %s\t%s", $idx, $pageTitle, $pageId));
                $fileName     = "$pageId";
                $storeAdapter = $this->prepareStoreAdapter($fileName);
                $storeAdapter->storeData($pageBody);

                $replaced = $this->replaceAndStoreDoctype($fileName, $pageBody, $oldDoctype, $newDoctype);
                if (!$replaced) {
                    self::$logger->error('ReplaceAndStoreDoctype failed', [$oldDoctype, $newDoctype, $fileName]);
                    break;
                }
                $idx++;
            }
        } else {
            self::$logger->notice('Response empty or Response->results empty');
        }

        return $modified;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareSettings(): void
    {
        // NothingToDo
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    final protected function validateSettings(Map $overrideParameters): bool
    {
        return true;
    }
}
