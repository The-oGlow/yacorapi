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

namespace oglow\tools\Yacorapi\Helper;

use Ds\Map;

class AbstractHelperTestDummyClazz extends AbstractHelper
{
    protected function prepareSettings(): void
    {
        // Nothing to do
    }

    /**
     * @param Map<mixed, mixed> $overrideParameters
     *
     * @return bool
     */
    protected function validateSettings(Map $overrideParameters): bool
    {
        if ($overrideParameters->isEmpty()) {
            return true;
        }

        return false;
    }

    // Change visibility

    /**
     * @param Map<mixed,mixed> $overrideParameters
     *
     * @return bool
     */
    public function publicValidateSettings(Map $overrideParameters): bool
    {
        return $this->validateSettings($overrideParameters);
    }
}
