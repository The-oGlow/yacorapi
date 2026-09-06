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

use Ds\Collection;

class AbstractHelperTestDummyClazz extends AbstractHelper
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareSettings(Collection $overrideParameters): void
    {
        // Nothing to do
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function validateSettings(Collection $overrideParameters): bool
    {
        $result = false;
        if ($overrideParameters->isEmpty()) {
            $result = true;
        }

        return $result;
    }

    // Change visibility

    /**
     * @param Collection<mixed,mixed> $overrideParameters
     *
     * @return bool
     */
    public function publicValidateSettings(Collection $overrideParameters): bool
    {
        return $this->validateSettings($overrideParameters);
    }
}
