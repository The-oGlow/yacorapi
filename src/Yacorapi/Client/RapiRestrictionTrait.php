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

use oglow\tools\Yacorapi\IResponse;

trait RapiRestrictionTrait
{
    /**
     * @inheritDoc
     */
    #[\Override]
    public function readRestrictionsByPageId(int $pageId): IResponse
    {
        self::$logger->debug('START - pageId', [$pageId]);

        $prepareUrl = $this->adminExtension->prepareRestrictByOpUrl($pageId);

        return $this->exec($prepareUrl);
    }

    /**
     * REFACTOR: API-Function doesn't work or description is wrong.
     *
     * @inheritDoc
     */
    #[\Override]
    public function writeRestrictionsByPageId(int $pageId, array $writeRestrictions = [], array $readRestrictions = []): bool // NOSONAR: php:S1172
    {
        throw new \BadMethodCallException('API-Function does not work or description is wrong');
    }
}
