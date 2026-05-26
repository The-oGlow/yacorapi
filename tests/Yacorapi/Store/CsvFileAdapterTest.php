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

use PHPUnit\Framework\EasyGoingTestCase;
use oglow\tools\Yacorapi\YacorapiTestData;
use Monolog\ConsoleLogger;
use Psr\Log\LoggerInterface;

class CsvFileAdapterTest extends EasyGoingTestCase
{
    private const RAND_MIN = 10;

    private const RAND_MAX = 30;

    private const CHAR_MIN = 64;

    private const CHAR_MAX = self::CHAR_MIN + 26;

    /** @var LoggerInterface */
    private static $logger;

    /** @var string */
    private static $fileName;

    public function __construct($name = null, $data = [], $dataName = '')
    {
        self::$logger = new ConsoleLogger(CsvFileAdapterTest::class);
        self::$logger->debug('START');

        parent::__construct($name, $data, $dataName);

        self::$logger->debug('END');
    }

    /**
     * @return CsvFileAdapter
     */
    protected static function prepareO2t()
    {
        return new CsvFileAdapter(self::$fileName);
    }

    /**
     * @return CsvFileAdapter
     */
    protected function getCasto2t()
    {
        return $this->o2t;
    }

    public function setUp(): void
    {
        self::$fileName = YacorapiTestData::FILE_FILENAME . '-' . microtime(true);
        $this->o2t = self::prepareO2t();
        self::$logger->info($this->getCasto2t()->getStoreItem());
    }

    public function testStoreDataHeader(): void
    {
        $dataHeader = [];
        for ($idx = 0; $idx < random_int(self::RAND_MIN, self::RAND_MAX); $idx++) {
            $dataHeader[] = 'COL' . $idx;
        }
        $expected = strlen(implode(';', $dataHeader)) + count($dataHeader) * 2 + 1;

        $this->getCasto2t()->storeDataHeader($dataHeader);

        $testFileName = $this->getCasto2t()->getStoreItem();
        self::assertFileExists($testFileName);
        self::assertEquals($expected, filesize($testFileName), "Filesize is not as expected for '$testFileName'");
    }

    public function testStoreData(): void
    {
        $dataContent = [];
        for ($idx = 0; $idx < random_int(self::RAND_MIN, self::RAND_MAX); $idx++) {
            $dataContent[] = chr(random_int(self::CHAR_MIN, self::CHAR_MAX));
        }
        $expected = strlen(implode(FileAdapter::C_ITEM_SEP, $dataContent)) + 1;

        $this->getCasto2t()->storeData($dataContent);

        $testFileName = $this->getCasto2t()->getStoreItem();
        self::assertFileExists($testFileName);
        self::assertEquals($expected, filesize($testFileName), "Filesize is not as expected for '$testFileName'");
    }
}
