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

use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class FileStoreItemTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): IStoreItem
    {
        return FileStoreItem::prepareTargetFile(
            YacorapiTestData::FILE_FOLDERNAME,
            YacorapiTestData::FILE_FILENAME,
            YacorapiTestData::FILE_EXT_NAME
        );
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): FileStoreItem
    {
        return $this->o2t;
    }

    public function testGetDir(): void
    {
        $expected = YacorapiTestData::FILE_FOLDERNAME;

        $actual = $this->getCasto2t()->getDir();
        self::assertEquals($expected, $actual);
    }

    public function testSetDir(): void
    {
        $expected = YacorapiTestData::FILE_FOLDERNAME_EMPTY;

        $actual = $this->getCasto2t()->getDir();
        self::assertNotEquals($expected, $actual);

        $this->getCasto2t()->setDir($expected);

        $actual = $this->getCasto2t()->getDir();
        self::assertEquals($expected, $actual);
    }

    public function testGetFile(): void
    {
        $expected = YacorapiTestData::FILE_FILENAME;

        $actual = $this->getCasto2t()->getFile();

        self::assertEquals($expected, $actual);
    }

    public function testSetFile(): void
    {
        $expected = YacorapiTestData::FILE_FILENAME_EMPTY;

        $actual = $this->getCasto2t()->getFile();
        self::assertNotEquals($expected, $actual);

        $this->getCasto2t()->setFile($expected);

        $actual = $this->getCasto2t()->getFile();
        self::assertEquals($expected, $actual);
    }

    public function testGetExt(): void
    {
        $expected = YacorapiTestData::FILE_EXT_NAME;

        $actual = $this->getCasto2t()->getExt();

        self::assertEquals($expected, $actual);
    }

    public function testSetExt(): void
    {
        $expected = YacorapiTestData::FILE_EXT_EMPTY;

        $actual = $this->getCasto2t()->getExt();
        self::assertNotEquals($expected, $actual);

        $this->getCasto2t()->setExt($expected);

        $actual = $this->getCasto2t()->getExt();
        self::assertEquals($expected, $actual);
    }

    public function testToString(): void
    {
        $expected = YacorapiTestData::FILE_FOLDERNAME . FileStoreItem::C_DIR_SEP .
        YacorapiTestData::FILE_FILENAME . FileStoreItem::C_FILE_SEP .
        YacorapiTestData::FILE_EXT_NAME;

        $actual = $this->getCasto2t()->__toString();

        self::assertEquals($expected, $actual);
    }
}
