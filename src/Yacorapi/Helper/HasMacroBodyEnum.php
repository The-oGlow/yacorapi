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

use ollily\Tools\Arrays\IDoubleBackedEnum;

enum HasMacroBodyEnum: string implements IDoubleBackedEnum
{
    case NONE = 'none';

    /**
     * List of macros having a plain body.
     */
    case PLAIN = 'plain';

    /**
     * List of macros having a rich body.
     */
    case RICH = 'rich';

    case CUSTOM = 'custom';

    public static function hasBody(string $macro): HasMacroBodyEnum
    {
        $result = self::NONE;
        foreach (self::cases() as $case) {
            $found = array_search(strtolower($macro), $case->objectValue(), true);
            if ($found !== false) {
                $result = $case;
                break;
            }
        }

        return $result;
    }

    #[\Override]
    public function intValue(): int
    {
        return -1;
    }

    #[\Override]
    public function objectValue(): mixed
    {
        return match ($this) {
            self::PLAIN => ['html', 'code'],
            self::RICH => ['section', 'column', 'panel'],
            self::CUSTOM => [],
            default => []
        };
    }

    #[\Override]
    public function text(): string
    {
        return $this->value;
    }
}
