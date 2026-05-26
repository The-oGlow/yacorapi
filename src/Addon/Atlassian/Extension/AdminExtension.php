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

namespace oglow\tools\Addon\Atlassian\Extension;

use oglow\tools\Yacorapi\Extension\AbstractExtension;
use oglow\tools\Yacorapi\Traits\PrepPermissionTrait;
use oglow\tools\Yacorapi\IConnectionProvider;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings("PHPMD.UnusedPrivateField")
 */
class AdminExtension extends AbstractExtension
{
    use PrepPermissionTrait;

    /** @var IConnectionProvider
     *  @psalm-suppress PropertyNotSetInConstructor
     */
    protected $connectionProvider;

    /** @var LoggerInterface */
    private static $logger;

    public static function getName(): string
    {
        return 'Atlassian Admin Extension';
    }

    public static function getId(): int
    {
        return 4;
    }
}
