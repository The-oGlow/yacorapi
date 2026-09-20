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

use Psr\Log\LogLevel;

/**
 * Interface for the store adapter.
 *
 * @author ollily
 */
interface IStoreAdapter
{
    /** @var string Default output level */
    public const string LEVEL_DEFAULT = LogLevel::INFO;

    /**
     * Store any data with the adapter.
     *
     * @param mixed $dataContent The content which will be stored
     */
    public function storeData(mixed $dataContent): void;

    /**
     * Store header data with the adapter.
     *
     * @param array<mixed>|string $dataHeader A header which will be stored
     */
    public function storeDataHeader(string|array $dataHeader): void;

    /**
     * Returns the full filename of the output file.
     *
     * @return string The full filename
     */
    public function getFileName(): string;
}
