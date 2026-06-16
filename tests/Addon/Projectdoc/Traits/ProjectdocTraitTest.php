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

namespace oglow\tools\Addon\Projectdoc\Traits;

use oglow\tools\Yacorapi\IResponse;
use oglow\tools\Yacorapi\Response\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\EasyGoingTestCase;

class ProjectdocTraitTest extends EasyGoingTestCase
{
    #[\Override]
    protected static function prepareO2t(): ProjectdocTraitTestClazz
    {
        return new ProjectdocTraitTestClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): ProjectdocTraitTestClazz
    {
        return $this->o2t;
    }

    /**
     * @param bool      $expected
     * @param IResponse $response
     */
    #[DataProvider('providerResponse')]
    public function testCheckDataPdtDocument(bool $expected, IResponse $response): void
    {
        $actual = $this->getCasto2t()->checkDataPdtDocument($response);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param bool      $expected
     * @param IResponse $response
     */
    #[DataProvider('providerResponse')]
    public function testCheckDataPdtProperty(bool $expected, IResponse $response): void
    {
        $actual = $this->getCasto2t()->checkDataPdtProperty($response);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param string    $expected
     * @param IResponse $response
     * @param string    $propertyName
     */
    #[DataProvider('providerPropertyName')]
    public function testshowResultsPdt(string $expected, IResponse $response, string $propertyName): void
    {
        $actual = $this->getCasto2t()->showResultsPdt($response, $propertyName);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param IResponse $expected
     * @param int       $pageId
     * @param string    $propertyName
     */
    #[DataProvider('providerPdtReadProperty')]
    public function testPdtReadProperty(IResponse $expected, int $pageId, string $propertyName): void
    {
        $actual = $this->getCasto2t()->pdtReadProperty($pageId, $propertyName);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param IResponse          $expected
     * @param array<mixed,mixed> $propertyNames
     * @param string             $spaceKey
     * @param string             $where
     */
    #[DataProvider('providerPdtReadDocument')]
    public function testPdtReadDocument(
        IResponse $expected,
        array $propertyNames,
        string $spaceKey,
        string $where
    ): void {
        $actual = $this->getCasto2t()->pdtReadDocument($propertyNames, $spaceKey, $where);

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerResponse(): array
    {
        return [
            'empty' => [ false, new Response()],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerPropertyName(): array
    {
        return [
            'empty' => [ '', new Response(), ''],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerPdtReadProperty(): array
    {
        return [
            'empty' => [ new Response(), 0,''],
        ];
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerPdtReadDocument(): array
    {
        return [
            'empty' => [ new Response(), [],'',''],
        ];
    }
}
