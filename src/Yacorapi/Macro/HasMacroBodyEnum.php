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

namespace oglow\tools\Yacorapi\Macro;

use ollily\Tools\Arrays\IDoubleBackedEnum;
use ollily\Tools\JsonHelper;

enum HasMacroBodyEnum: string implements IDoubleBackedEnum {

    /** Macro has no body. */
    case NONE = 'none';

    /** Macro has a plain body. */
    case PLAIN = 'plain';

    /** Macro has a rich body. */
    case RICH = 'rich';
    
    /** Macro has a specialized body. */
    case CUSTOM = 'custom';

    public static function hasBody(string $macro): HasMacroBodyEnum {
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
    public function intValue(): int {
        return -1;
    }

    #[\Override]
    public function objectValue(): mixed {
        return match ($this) {
            self::PLAIN => ['csv','html', 'code','json-table','projectdoc-code-block-placeholder-macro','projectdoc-hide','sp-plaintextbody-link', 'um-iframe'],
            
            self::RICH => ['column', 'document-properties-macro', 'multiexcerpt-fast-block-macro', 'panel', 'projectdoc-aside-panel-macro', 
                'projectdoc-box-caution', 'projectdoc-box-deprecated', 'projectdoc-box-example', 'projectdoc-box-feedback', 'projectdoc-box-info', 
                'projectdoc-box-note', 'projectdoc-box-tip', 'projectdoc-box-warning', 'projectdoc-content-marker', 'projectdoc-definition-list-macro', 
                'projectdoc-hide-from-reader-macro', 'projectdoc-layout-element-macro', 'projectdoc-link-external', 'projectdoc-link-wiki', 
                'projectdoc-section', 'projectdoc-table-merger-macro', 'projectdoc-transclusion-property-display', 'section', 'table-chart', 
                'table-filter', 'tour-macro', 'unmigrated-wiki-markup'
                ],
            self::CUSTOM => [],
            default => []
        };
    }

    #[\Override]
    public function text(): string {
        return $this->value;
    }
}
