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

namespace oglow\tools\Addon\ThirdParty\Macro;

use Ds\Vector;
use oglow\tools\Yacorapi\Macro\AbstractAddon;

/**
 * Class ThirdPartyMacro.
 *
 * Replace in the method {@link getMacros()} with your 3rd party addons and macros.
 */
class ThirdPartyAddon extends AbstractAddon
{
    /**
     * @SuppressWarnings("PHPMD.ExcessiveMethodLength")
     */
    #[\Override]
    protected function init(): void // NOSONAR: php:S138
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
                        'attachment-table',
                        'csv',
                        'jql-table',
                        'json-table',
                        'table-plus',
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
                'Draw.io Confluence Plugin'                            => new Vector(
                    [
                        'drawio',
                        'drawio-sketch',
                        'inc-drawio',
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
                        'undefined-links,',
                    ]
                ),
                'Scroll Documents for Confluence'                      => new Vector(
                    [
                        'scroll-document-location',
                    ]
                ),
                'Scroll Exporter Extensions'                           => new Vector(
                    [
                        'scroll-bookmark',
                        'scroll-content-block',
                        'scroll-exportbutton',
                        'scroll-ignore',
                        'scroll-ignore-inline',
                        'scroll-indexterm',
                        'scroll-landscape',
                        'scroll-only',
                        'scroll-only-inline',
                        'scroll-pagebreak',
                        'scroll-portrait',
                        'scroll-pagetitle',
                        'scroll-tablelayout',
                        'scroll-title',
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
