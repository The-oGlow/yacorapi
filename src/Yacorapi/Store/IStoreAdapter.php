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

namespace oglow\tools\Yacorapi\Store;

/**
 * Interface IStoreAdapter.
 */
interface IStoreAdapter
{
    public const string KEY_KEY    = 'key';

    public const string KEY_LINKS  = '_links';

    public const string KEY_TINYUI = 'tinyui';

    public const string KEY_TITLE  = 'title';

    /**
     * @param mixed $dataContent
     */
    public function storeData(mixed $dataContent): void;

    /**
     * @param string|string[] $dataHeader
     */
    public function storeDataHeader(string|array $dataHeader): void;
}
