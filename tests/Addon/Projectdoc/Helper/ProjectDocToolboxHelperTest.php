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

namespace oglow\tools\Addon\Projectdoc\Helper;

use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Response\Response;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;

class ProjectDocToolboxHelperTest extends EasyGoingTestCase
{
    private string $cleanupFile = '';

    #[\Override]
    protected static function prepareO2t(): ProjectDocToolboxHelper
    {
        return new ProjectDocToolboxHelper();
    }

    #[\Override]
    protected function getCasto2t(): ProjectDocToolboxHelper
    {
        return $this->o2t;
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        YacorapiTestData::cleanupTempFile($this->cleanupFile);
    }

    /**
     * @param IResponse $response
     * @param string    $oldDoctype
     * @param string    $newDoctype
     * @param bool      $expected
     *
     * @dataProvider providerModifyData
     */
    public function testModifyData(IResponse $response, string $oldDoctype, string $newDoctype, bool $expected): void
    {
        $actual = $this->getCasto2t()->modifyData($response, $oldDoctype, $newDoctype);

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<mixed,array<mixed,mixed>>
     */
    public function providerModifyData(): array
    {
        return [
            'RequestNew' => [new Response(), YacorapiTestData::MACR_DOCTYPE_OLD, YacorapiTestData::MACR_DOCTYPE_NEW, false],
        ];
    }

    /**
     * @param string $fileName
     * @param string $body
     * @param string $oldDoctype
     * @param string $newDoctype
     * @param bool   $expected
     *
     * @dataProvider providerReplaceAndStoreDoctype
     */
    public function testReplaceAndStoreDoctype(string $fileName, string $body, string $oldDoctype, string $newDoctype, bool $expected): void
    {
        $this->cleanupFile = $fileName;
        $actual = $this->getCasto2t()->replaceAndStoreDoctype($fileName, $body, $oldDoctype, $newDoctype);

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<mixed,array<mixed,mixed>>
     */
    public function providerReplaceAndStoreDoctype(): array
    {
        return [
            'FilenameWrongBodyEmpty' => [
                YacorapiTestData::FILE_FILENAME_EMPTY,
                YacorapiTestData::MACR_BODY_EMPTY,
                YacorapiTestData::MACR_DOCTYPE_OLD,
                YacorapiTestData::MACR_DOCTYPE_NEW,
                false,
            ],
            'FilenameWrongBodySimple' => [
                YacorapiTestData::FILE_FILENAME_EMPTY,
                YacorapiTestData::MACR_BODY_SIMPLE,
                YacorapiTestData::MACR_DOCTYPE_OLD,
                YacorapiTestData::MACR_DOCTYPE_NEW,
                false,
            ],
            'FilenameCorrectBodyEmpty' => [
                YacorapiTestData::prepareTempFile(),
                YacorapiTestData::MACR_BODY_EMPTY,
                YacorapiTestData::MACR_DOCTYPE_OLD,
                YacorapiTestData::MACR_DOCTYPE_NEW,
                false,
            ],
            'FilenameCorrectBodySimple' => [
                YacorapiTestData::prepareTempFile(),
                YacorapiTestData::MACR_BODY_SIMPLE,
                YacorapiTestData::MACR_DOCTYPE_OLD,
                YacorapiTestData::MACR_DOCTYPE_NEW,
                false,
            ],
        ];
    }

    /**
     * @param string $body
     * @param string $oldDoctype
     * @param string $newDoctype
     * @param bool   $isContains
     *
     * @dataProvider providerReplaceDoctype
     */
    public function testReplaceDoctype(string $body, string $oldDoctype, string $newDoctype, bool $isContains): void
    {
        $actual = $this->getCasto2t()->replaceDoctype($body, $oldDoctype, $newDoctype);

        self::assertNotNull($actual);
        if ($isContains) {
            self::assertStringContainsString($newDoctype, $actual);
        } else {
            self::assertStringNotContainsString($newDoctype, $actual);
        }
    }

    /**
     * @return array<mixed,array<mixed,mixed>>
     */
    public function providerReplaceDoctype(): array
    {
        return [
            'BodyEmpty'              => [YacorapiTestData::MACR_BODY_EMPTY, YacorapiTestData::MACR_DOCTYPE_OLD, YacorapiTestData::MACR_DOCTYPE_NEW, false],
            'BodyInvalid'            => [YacorapiTestData::MACR_BODY_INVALID, YacorapiTestData::MACR_DOCTYPE_OLD, YacorapiTestData::MACR_DOCTYPE_NEW, false],
            'BodySimple'             => [YacorapiTestData::MACR_DOCTYPE_OLD, YacorapiTestData::MACR_DOCTYPE_OLD, YacorapiTestData::MACR_DOCTYPE_NEW, false],
            'DoctypeEmpty'           => [
                YacorapiTestData::repPH(
                    YacorapiTestData::getMacroCode(YacorapiTestData::MACR_PROJECTDOC_PROPERTIES_MARKER),
                    YacorapiTestData::MACR_DOCTYPE_EMPTY
                ),
                YacorapiTestData::MACR_DOCTYPE_OLD,
                YacorapiTestData::MACR_DOCTYPE_NEW,
                false,
            ],
            'DoctypeInvalid'         => [
                YacorapiTestData::repPH(
                    YacorapiTestData::getMacroCode(YacorapiTestData::MACR_PROJECTDOC_PROPERTIES_MARKER),
                    YacorapiTestData::MACR_DOCTYPE_WRONG
                ),
                YacorapiTestData::MACR_DOCTYPE_OLD,
                YacorapiTestData::MACR_DOCTYPE_NEW,
                false,
            ],
            'DoctypeCorrect'         => [
                YacorapiTestData::repPH(
                    YacorapiTestData::getMacroCode(YacorapiTestData::MACR_PROJECTDOC_PROPERTIES_MARKER),
                    YacorapiTestData::MACR_DOCTYPE_OLD
                ),
                YacorapiTestData::MACR_DOCTYPE_OLD,
                YacorapiTestData::MACR_DOCTYPE_NEW,
                true,
            ],
            'DoctypeAlreadyReplaced' => [
                YacorapiTestData::repPH(
                    YacorapiTestData::getMacroCode(YacorapiTestData::MACR_PROJECTDOC_PROPERTIES_MARKER),
                    YacorapiTestData::MACR_DOCTYPE_NEW
                ),
                YacorapiTestData::MACR_DOCTYPE_OLD,
                YacorapiTestData::MACR_DOCTYPE_NEW,
                true,
            ],
        ];
    }
}
