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

use Ds\Set;
use oglow\tools\Yacorapi\IResponse;

interface IRapiClientPermission extends IRapiClientWrite
{
    /**
     * Load the restrictions for this confluence page.
     *
     * @param int $pageId The id of the confluence page
     *
     * @return IResponse The page restrictions for the page or empty
     */
    public function readRestrictionsByPageId(int $pageId): IResponse;

    /**
     * Set page restrictions (read/write) for the confluence page.
     * REFACTOR: API-Function doesn't work or description is wrong.
     *
     * @param int                $pageId            The id of the confluence page
     * @param array<mixed,mixed> $writeRestrictions Write restrictions for the page
     * @param array<mixed,mixed> $readRestrictions  Read restrictions for the page
     *
     * @return bool TRUE=Restrictions are set properly, else FALSE
     */
    public function writeRestrictionsByPageId(int $pageId, array $writeRestrictions = [], array $readRestrictions = []): bool;
}
