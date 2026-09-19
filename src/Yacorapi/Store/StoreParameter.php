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

use ollily\Common\AbstractSingleton;

/**
 * Description of StoreParameter.
 *
 * @author ollily
 */
class StoreParameter extends AbstractSingleton
{
    public const string KEY_KEY = 'key';

    public const string KEY_LINKS = '_links';

    public const string KEY_TINYUI = 'tinyui';

    public const string KEY_TITLE = 'title';

    public const bool C_DIR_RECURSIVE = true;

    public const int C_DIR_MASK = 0o777;

    public const string C_FILE_UTF8 = 'UTF-8';

    public const int C_FILE_LINE_LEN = 1000;

    public const string C_FILE_READ = 'r';

    public const string C_FILE_SEP = '.';

    public const string C_FILE_EOL = "\n";

    public const string C_FILE_EXT_TEXT = 'txt';

    public const string C_FILE_EXT_CSV = 'csv';

    public const string C_DIR_SEP_UNIX = '/';

    public const string C_DIR_SEP_WIN = '\\';

    /** @var array<mixed> Illegal chars for a filename */
    public const array C_ILLEGAL_FILE_CHARS = ['\\', '/', ':', '@', '?', '*'];

    public const int ERR_NOT_INVOKED = 30;

    /** @var string column separator */
    public const string DEFAULT_ITEM_SEP = ';';

    /** @var string text separator */
    public const string DEFAULT_COLUMN_TEXT_SEP = '"';

    public const string DEFAULT_FILE_PREFIX = '';

    public const string DEFAULT_FILE_SUFFIX = '';

    public const string DEFAULT_FILE_EXT = '';

    public const string DEFAULT_FOLDER_NAME = '';

    public const string DEFAULT_STORE_ITEM_CLAZZ = FileStoreItem::class;

    public const string DEFAULT_STORE_ITEM_METHOD = 'prepareTargetFile';

    public const string DEFAULT_SQUARE_BRACK_OPEN = '[';

    public const string DEFAULT_SQUARE_BRACK_CLOSE = ']';
}
