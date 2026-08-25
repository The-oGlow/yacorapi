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

namespace oglow\tools\Addon\Atlassian\Macro;

use Ds\Vector;
use oglow\tools\Yacorapi\Macro\AbstractAddon;

class AtlassianAddon extends AbstractAddon
{
    /**
     * @SuppressWarnings("PHPMD.ExcessiveMethodLength")
     */
    #[\Override]
    protected function init(): void  // NOSONAR: php:S138
    {
        parent::init();
        $this->addonsMacros->putAll(
            [
                'Atlassian Confluence Macros' => new Vector(
                    [
                        'anchor',
                        'attachments',
                        'blog-posts',
                        'calendar',
                        'change-history',
                        'chart',
                        'children',
                        'code',
                        'column',
                        'content-by-user',
                        'content-report-table',
                        'contentbylabel',
                        'contributors',
                        'create-from-template',
                        'details',
                        'detailssummary',
                        'excerpt',
                        'excerpt-include',
                        'expand',
                        'favpages',
                        'gadget',
                        'gallery',
                        'include',
                        'info',
                        'jira',
                        'jirachart',
                        'listlabels',
                        'livesearch',
                        'loremipsum',
                        'noformat',
                        'note',
                        'pagetree',
                        'pagetreesearch',
                        'panel',
                        'profile',
                        'profile-picture',
                        'recently-updated',
                        'roadmap',
                        'sharelinks-urlmacro',
                        'search',
                        'section',
                        'spaces',
                        'status',
                        'tasks-report-macro',
                        'tip',
                        'toc',
                        'toc-zone',
                        'unmigrated-wiki-markup',
                        'userlister',
                        'view-file',
                        'viewdoc',
                        'viewpdf',
                        'viewppt',
                        'viewxls',
                        'warning',
                        'widget',
                    ]
                ),
                'Confluence HTML Macros'            => new Vector(
                    [
                        'html',
                        'html-include',
                        'rss',
                        'rss-include',
                    ]
                ),
            ]
        );
    }
}
