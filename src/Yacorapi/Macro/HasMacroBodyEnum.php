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

enum HasMacroBodyEnum: string implements IDoubleBackedEnum
{
    /**
     * Macro has no body.
     */
    case NONE = 'none';

    /**
     * Macro has a plain body.
     */
    case PLAIN = 'plain';

    /**
     * Macro has a rich body.
     */
    case RICH = 'rich';

    /**
     * Macro has a specialized body.
     */
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
            self::PLAIN => [
                'code',
                'csv',
                'html',
                'html-include',
                'json-table',
                'noformat',
                'projectdoc-code-block-placeholder-macro',
                'projectdoc-hide',
                'projectdoc-index-entries-table-macro',
                'projectdoc-properties-supply-by-attachment-macro',
                'scroll-ignore-inline',
                'scroll-only-inline',
                'scroll-pagetitle',
                'sp-plaintextbody-link',
                'um-css-style',
                'unmigrated-wiki-markup',
                ],
            self::RICH => [
                'ai-table',
                'chart',
                'chart-plugin',
                'column',
                'details',
                'excerpt',
                'expand',
                'gadget',
                'info',
                'note',
                'orphaned-links',
                'panel',
                'pivot-table',
                'projectdoc-aside-panel-macro',
                'projectdoc-box-caution',
                'projectdoc-box-deprecated',
                'projectdoc-box-example',
                'projectdoc-box-fault',
                'projectdoc-box-feedback',
                'projectdoc-box-generic',
                'projectdoc-box-info',
                'projectdoc-box-note',
                'projectdoc-box-pending',
                'projectdoc-box-question',
                'projectdoc-box-references',
                'projectdoc-box-tip',
                'projectdoc-box-version',
                'projectdoc-box-warning',
                'projectdoc-content-marker',
                'projectdoc-count-macro',
                'projectdoc-definition-list-macro',
                'projectdoc-hide-from-anonymous-user-macro',
                'projectdoc-hide-from-reader-macro',
                'projectdoc-layout-element-macro',
                'projectdoc-link-external',
                'projectdoc-properties-marker',
                'projectdoc-properties-supplier-macro',
                'projectdoc-quote',
                'projectdoc-quote-external',
                'projectdoc-section',
                'projectdoc-steps-macro',
                'projectdoc-story-points',
                'projectdoc-story-relevance',
                'projectdoc-story-status',
                'projectdoc-table-merger-macro',
                'projectdoc-table-set-macro',
                'projectdoc-tour-macro',
                'projectdoc-transclude-documents-macro',
                'projectdoc-transclusion-macro',
                'projectdoc-transclusion-ref-macro',
                'scroll-content-block',
                'scroll-ignore',
                'scroll-only',
                'scroll-title',
                'section',
                'sp-image',
                'sp-pagelayout',
                'sp-pagelayout-cell',
                'sp-pagelayout-section',
                'sp-richtextbody-link',
                'spreadsheet-body-table',
                'table-chart',
                'table-excerpt',
                'table-filter',
                'table-joiner',
                'table-plus',
                'tip',
                'toc-zone',
                'undefined-links,',
                'warning',
                'workflow-reporter',
            ],
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
