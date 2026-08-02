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

use oglow\tools\Addon\Atlassian\Extension\AdminExtension;
use oglow\tools\Addon\Atlassian\Extension\AtlassianExtension;
use oglow\tools\Addon\Projectdoc\Extension\ProjectdocExtension;
use oglow\tools\Addon\ThirdParty\Extension\ThirdPartyExtension;
use oglow\tools\Addon\UserMacro\Extension\UserMacroExtension;
use ollily\Tools\Arrays\IDoubleBackedEnum;

enum ExtensionEnum: int implements IDoubleBackedEnum
{
    case EXTENSION_RAPI_CLIENT = 1;
    case EXTENSION_ATLASSIAN = 2;
    case EXTENSION_ATLASSIAN_ADMIN = 4;
    case EXTENSION_ATLASSIAN_USER_MACRO = 8;
    case EXTENSION_THIRD_PARTY = 16;
    case EXTENSION_PROJECTDOC_TOOLBOX = 32;
    case EXTENSION_MIN = self::EXTENSION_RAPI_CLIENT->value + self::EXTENSION_ATLASSIAN->value;
    case EXTENSION_ALL = self::EXTENSION_MIN->value +
    self::EXTENSION_ATLASSIAN_ADMIN->value +
    self::EXTENSION_ATLASSIAN_USER_MACRO->value +
    self::EXTENSION_THIRD_PARTY->value +
    self::EXTENSION_PROJECTDOC_TOOLBOX->value;

    /**
     * @inheritDoc
     */
    #[\Override]
    public function intValue(): int
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function objectValue(): mixed
    {
        /** @var null|IExtension */
        $newInstance = null;
        $clazz = $this->text();
        if (!empty($clazz)) {
            $newInstance = new $clazz();
        }

        return $newInstance;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function text(): string
    {
        return match ($this) {
            self::EXTENSION_RAPI_CLIENT => RapiClientExtension::class,
            self::EXTENSION_ATLASSIAN => AtlassianExtension::class,
            self::EXTENSION_ATLASSIAN_ADMIN => AdminExtension::class,
            self::EXTENSION_ATLASSIAN_USER_MACRO => UserMacroExtension::class,
            self::EXTENSION_THIRD_PARTY => ThirdPartyExtension::class,
            self::EXTENSION_PROJECTDOC_TOOLBOX => ProjectdocExtension::class,
            default => ''
        };
    }

    /**
     * @return array<ExtensionEnum>
     */
    public static function casesExtensions(): array
    {
        $callback = function (ExtensionEnum $extension): bool {
            $notAllowed = [self::EXTENSION_MIN,self::EXTENSION_ALL];

            return !in_array($extension, $notAllowed, true);
        };

        return array_filter(self::cases(), $callback);
    }

    /**
     * @param ExtensionEnum $extension The extension to compare with
     *
     * @return bool TRUE=this Enum is or contains by {@link $extension}
     */
    public function isIn(ExtensionEnum $extension): bool
    {
        $evaluated = $this->intValue() & $extension->intValue();

        return $evaluated == $this->value;
    }
}
