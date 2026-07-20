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

use Ds\Vector;

/**
 * Replace in the method {@link getMacros()} with your addons and macros which blocks your tasks.
 */
class BlockerAddon extends AbstractAddon
{
    public const AddonTypeEnum addonType = AddonTypeEnum::ADDON_BLOCKER;

    /**
     * @SuppressWarnings("PHPMD.ExcessiveMethodLength")
     */
    protected function init(): void
    {
        parent::init();
        $this->addonsMacros->putAll(
            [
                'Advanced Roadmaps for Jira in Confluence'             => new Vector(
                    [
                        'portfolio-for-jira-plan',
                    ]
                ),
                'Advanced Tables for Confluence'                       => new Vector(
                    [
                        'json-table',
                        'table-plus',
                        'csv',
                        'jql-table',
                    ]
                ),
                'BPMN Modeler Enterprise'                              => new Vector(
                    [
                        'vbcp-macro-enterprise',
                        'chart-plugin',
                        'chart',
                    ]
                ),
                'Comala Document Management'                           => new Vector(
                    [
                        'document-actions-report',
                        'document-approvals-report',
                        'document-states-report',
                        'document-stats-report',
                        'document-tasks-report',
                        'get-metadata',
                        'pageactivity',
                        'pagestatus',
                        'workflowreport',
                        'workflow-reporter',
                    ]
                ),
                'Confluence HTML Macros'                               => new Vector(
                    [
                        'html',
                        'html-include',
                        'rss',
                        'rss-include',
                    ]
                ),
                'Linking for Confluence'                               => new Vector(
                    [
                        'add-page',
                        'add-page-form',
                        'child-counter',
                        'incoming-links',
                        'link-page',
                        'link-to',
                        'link-window',
                        'orphaned-links',
                        'outgoing-links',
                        'undefined-links',
                    ]
                ),
                'projectdoc Toolbox for Confluence'                   => new Vector(
                    [
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
                        'projectdoc-code-block-placeholder-macro',
                        'projectdoc-content-marker',
                        'projectdoc-definition-list-macro',
                        'projectdoc-display-list',
                        'projectdoc-display-table',
                        'projectdoc-display-template-list',
                        'projectdoc-layout-element-macro',
                        'projectdoc-link-external',
                        'projectdoc-name-list',
                        'projectdoc-properties-marker',
                        'projectdoc-properties-supplier-macro',
                        'projectdoc-section',
                        'projectdoc-table-merger-macro',
                        'projectdoc-tour-by-property-macro',
                        'projectdoc-tour-macro',
                        'projectdoc-transclude-documents-macro',
                        'projectdoc-transclusion-macro',
                    ]
                ),
                'Scroll Documents for Confluence'                      => new Vector(
                    [
                        'scroll-document-location',
                    ]
                ),
                'Scroll Versions for Confluence'                       => new Vector(
                    [
                        'excerpt-includeplus',
                        'includeplus',
                        'sv-pagetree',
                    ]
                ),
                'Scroll Runtime for Confluence'                        => new Vector(
                    [
                        'sp-image',
                        'sp-nobody-link',
                        'sp-pagelayout',
                        'sp-pagelayout-cell',
                        'sp-pagelayout-section',
                        'sp-plaintextbody-link',
                        'sp-richtextbody-link',
                    ]
                ),
                'Table Filter, Charts and Spreadsheets for Confluence' => new Vector(
                    [
                        'ai-table',
                        'csv-table',
                        'json-from-table',
                        'pivot-table',
                        'spreadsheet-body-table',
                        'spreadsheet-include',
                        'spreadsheet-table',
                        'table-chart',
                        'table-excerpt',
                        'table-excerpt-include',
                        'table-filter',
                        'table-joiner',
                    ]
                ),
            ]
        );
    }
}
