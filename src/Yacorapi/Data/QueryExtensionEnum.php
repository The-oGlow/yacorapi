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

namespace oglow\tools\Yacorapi\Data;

/**
 * @author postm
 */
enum QueryExtensionEnum: string
{
    case REQP_SPACE_LIST = 'expand=homepage,description.plain,metadata.labels';
    case REQP_FULL = 'expand=space,history,version,body.storage,restrictions.read.restrictions.user,' .
        'restrictions.read.restrictions.group,restrictions.read,restrictions.update.restrictions.user,' .
        'restrictions.update.restrictions.group';
    case REQP_SEARCH_FULL = 'expand=content.space,content.history,content.version,content.body.storage';
    case REQP_LIGHT = 'expand=space,history,version';
    case REQP_PERM = 'expand=restrictions.read.restrictions.user,restrictions.read.restrictions.group,' .
        'restrictions.read,restrictions.update.restrictions.user,restrictions.update.restrictions.group';
    case REQP_SEARCH_LIGHT = 'expand=content.space,content.history,content.version';
    case REQP_RESTRICTIONS_FULL = 'expand=read.restrictions.user,read.restrictions.group,update.restrictions.user,update.restrictions.group';
    case RESP_CSV_SPACE_RESULTS = ' .results[]|.key + ";" + .type + ";" + "status" + ";"'
        . ' + "\"" + .name + "\"" + ";" + "\"" + .description.plain.value +';
}
