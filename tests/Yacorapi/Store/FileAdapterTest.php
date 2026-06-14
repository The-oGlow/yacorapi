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

use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;

class FileAdapterTest extends EasyGoingTestCase
{
    private const int RAND_MIN = 10;

    private const int RAND_MAX = 30;

    private const int CHAR_MIN = 64;

    private const int CHAR_MAX = self::CHAR_MIN + 26;

    private static LoggerInterface $logger;

    private static string $fileName;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        self::$logger = new ConsoleLogger(FileAdapterTest::class);
        self::$logger->debug('START');

        parent::setUpBeforeClass();

        self::$logger->debug('END');
    }

    #[\Override]
    protected static function prepareO2t(): FileAdapter
    {
        return new FileAdapter(self::$fileName);
    }

    #[\Override]
    protected function getCasto2t(): FileAdapter
    {
        return $this->o2t;
    }

    #[\Override]
    public function setUp(): void
    {
        self::$fileName = YacorapiTestData::FILE_FILENAME . '-' . microtime(true);
        $this->o2t = self::prepareO2t();
        self::$logger->info($this->getCasto2t()->getStoreItem());
    }

    public function testStoreResults(): void
    {
        $results = [
            FileAdapter::KEY_KEY => YacorapiTestData::C_PAGEID_EXIST,
            FileAdapter::KEY_TITLE => YacorapiTestData::FILE_EXT_JSON,
        ];
        $results[FileAdapter::KEY_LINKS][FileAdapter::KEY_TINYUI] = YacorapiTestData::FILE_EXT_JSON;
        $expected = 42;

        $this->getCasto2t()->storeResults($results);

        $testFileName = $this->getCasto2t()->getStoreItem();
        self::assertFileExists($testFileName);
        self::assertGreaterThanOrEqual($expected, filesize($testFileName), "Filesize is smaller as expected for '$testFileName'");
    }

    public function testStoreDataHeader(): void
    {
        $dataHeader = '';
        for ($idx = 0; $idx < random_int(self::RAND_MIN, self::RAND_MAX); $idx++) {
            $dataHeader .= 'COL1' . $idx;
        }
        $expected = strlen($dataHeader) + 1;

        $this->getCasto2t()->storeDataHeader($dataHeader);

        $testFileName = $this->getCasto2t()->getStoreItem();
        self::assertFileExists($testFileName);
        self::assertEquals($expected, filesize($testFileName), "Filesize is not as expected for '$testFileName'");
    }

    public function testStoreData(): void
    {
        $dataContent = '';
        for ($idx = 0; $idx < random_int(self::RAND_MIN, self::RAND_MAX); $idx++) {
            $dataContent .= chr(random_int(self::CHAR_MIN, self::CHAR_MAX));
        }
        $expected = strlen($dataContent) + 1;

        $this->getCasto2t()->storeData($dataContent);

        $testFileName = $this->getCasto2t()->getStoreItem();
        self::assertFileExists($testFileName);
        self::assertEquals($expected, filesize($testFileName), "Filesize is not as expected for '$testFileName'");
    }
}
