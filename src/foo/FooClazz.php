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

namespace foo;

/**
 * Class FooClazz. 
 * All code checks are deactivated.
 *
 * @SuppressWarnings("PHPMD")
 */
class FooClazz
{
    private string $privateFoo = 'privateFooValue'; // NOSONAR

    protected function protectedFoo(): string
    {
        return 'protectedFooMethod';
    }

    public function isValid(): bool
    {
        return true;
    }
}
