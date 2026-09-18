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

namespace oglow\tools\common;

use Ds\Collection;

/**
 * @author olliy
 */
class AbstractSingletonTestDummyClazz extends AbstractSingleton
{
    // Change visibility

    /**
     * @param Collection<mixed,mixed> $overrideParameters
     */
    public function publicPrepareSettings(Collection $overrideParameters): void
    {
        $this->prepareSettings($overrideParameters);
    }

    /**
     * @param Collection<mixed,mixed> $overrideParameters
     *
     * @return bool
     */
    public function publicValidateSettings(Collection $overrideParameters): bool
    {
        return $this->validateSettings($overrideParameters);
    }

    /**
     * @return string
     */
    public function publicPrepareShortOpts(): string
    {
        return $this->prepareShortOpts();
    }

    /**
     * @return array<mixed>
     */
    public function publicPrepareLongOpts(): array
    {
        return $this->prepareLongOpts();
    }

    /**
     * @param Collection<mixed, mixed> $overrideParameters
     * @param string                   $keyName
     *
     * @return mixed
     */
    public function publicParseBoolCollection(Collection $overrideParameters, string $keyName): mixed
    {
        return $this->parseBool($overrideParameters, $keyName);
    }
}
