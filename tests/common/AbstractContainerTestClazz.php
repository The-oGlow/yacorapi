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

class AbstractContainerTestClazz extends AbstractContainer
{
    public const TEST_MODE = 1;

    public const DATA_A = 'a';

    public const DATA_B = 'b';

    protected function prepareModes(): void
    {
        $this->setModes([self::TEST_MODE]);
    }

    protected function prepareData(): void
    {
        $allData                     = [];
        $allData[self::DATA_A]                     = [self::DATA_A];
        $allData[self::DATA_B]                     = [self::DATA_B];

        $this->setAllData($allData);
    }

    // Set method to public for testing purpose

    /**
     * @return array<mixed,mixed>
     */
    public function publicToStringValues(): array
    {
        return parent::__toStringValues();
    }
}
