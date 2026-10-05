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

namespace oglow\tools\Yacorapi\Extension;

use oglow\tools\Yacorapi\Macro\AbstractExtensionTestAddon;
use oglow\tools\Yacorapi\YacorapiTestData;

class AbstractExtensionTestClazz extends AbstractExtension
{
    #[\Override]
    protected function init(): void
    {
        parent::init();
        $this->addons = new AbstractExtensionTestAddon();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function getName(): string
    {
        return YacorapiTestData::NOTEXIST_NAME;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function getId(): int
    {
        return YacorapiTestData::NOTEXIST_ID;
    }
}
