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

namespace oglow\tools\Yacorapi\Extension;

use Ds\Map;
use Ds\Vector;
use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;

class ExtensionTraitTest extends EasyGoingTestCase
{
    private static LoggerInterface $logger;

    #[\Override]
    protected static function prepareO2t(): ExtensionTraitTestClazz
    {
        return new ExtensionTraitTestClazz();
    }

    #[\Override]
    protected function getCasto2t(): ExtensionTraitTestClazz
    {
        return $this->o2t;
    }

    #[\Override]
    public function setUp(): void
    {
        self::$logger = new ConsoleLogger(ExtensionTraitTest::class);
        self::$logger->debug('START');
        parent::setUp();
        self::$logger->debug('END');
    }

    /**
     * @return Map<mixed,IExtension>
     */
    protected function getPublicInitExtensions(): Map
    {
        $modeExtension = ExtensionEnum::EXTENSION_ALL;

        return $this->getCasto2t()->publicInitExtensions($modeExtension);
    }

    public function testInitExtensions(): void
    {
        $expectedExtensions = ExtensionEnum::casesExtensions();
        $expectedSize = YacorapiTestData::EXTENSIONS_COUNT_TOTAL;

        $actual = $this->getPublicInitExtensions();

        self::assertCount($expectedSize, $actual);
        foreach ($expectedExtensions as $extensionEnum) {
            /** @var class-string */
            $clazzName = $extensionEnum->text();
            self::assertInstanceOf($clazzName, $actual->get($extensionEnum->value));
        }
    }

    public function testLoadExtensions(): void
    {
        $expectedExtensions = ExtensionEnum::casesExtensions();
        $expectedSize = YacorapiTestData::EXTENSIONS_COUNT_TOTAL;
        $modeExtension = ExtensionEnum::EXTENSION_ALL;

        $actual = $this->getCasto2t()->publicLoadExtensions($modeExtension);

        self::assertCount($expectedSize, $actual);
        foreach ($expectedExtensions as $extensionEnum) {
            /** @var class-string */
            $clazzName = $extensionEnum->text();
            self::assertInstanceOf($clazzName, $actual->get($extensionEnum->value));
        }
    }

    public function testGetExtensionAddons(): void
    {
        $extensions = $this->getPublicInitExtensions();

        $expectedSize   = YacorapiTestData::ADDONS_COUNT_TOTAL;
        $expectedAddons = YacorapiTestData::ADDONS_NAMES;

        $actual = $this->getCasto2t()->publicGetExtensionAddons($extensions);

        self::assertCount($expectedSize, $actual);
        $addons = $actual->keys();
        foreach ($expectedAddons as $expectedAddon) {
            self::assertContains($expectedAddon, $addons);
            $addons->remove($expectedAddon);
        }
        self::assertTrue($addons->isEmpty(), print_r($addons, true));
    }

    public function testGetExtensionAddonMacros(): void
    {
        $extensions = $this->getPublicInitExtensions();
        $addons     = $this->getCasto2t()->publicGetExtensionAddons($extensions);

        $expectedSize   = YacorapiTestData::MACROS_COUNT_TOTAL;
        $expectedMacros = YacorapiTestData::MACROS_VERIFY;

        $actual = $this->getCasto2t()->publicGetExtensionAddonMacros($addons);

        self::assertInstanceOf(Vector::class, $actual);
        self::assertCount($expectedSize, $actual);
        foreach ($expectedMacros as $expectedMacro) {
            self::assertContains($expectedMacro, $actual);
        }
    }

    public function testGetExtensionAddonMacrosArray(): void
    {
        $extensions = $this->getPublicInitExtensions();
        $addons     = $this->getCasto2t()->publicGetExtensionAddons($extensions);

        $expectedSize   = YacorapiTestData::MACROS_COUNT_TOTAL;
        $expectedMacros = YacorapiTestData::MACROS_VERIFY;

        $actual = $this->getCasto2t()->publicGetExtensionAddonMacrosArray($addons);

        self::assertIsArray($actual);
        self::assertCount($expectedSize, $actual);
        foreach ($expectedMacros as $expectedMacro) {
            self::assertContains($expectedMacro, $actual);
        }
    }

    #[DataProvider('providerGetExtension')]
    public function testGetExtension(bool $expected, ExtensionEnum $extension): void
    {
        $modeExtension = ExtensionEnum::EXTENSION_ALL;
        $this->getCasto2t()->publicLoadExtensions($modeExtension);

        $actual = $this->getCasto2t()->publiGetExtension($extension);

        self::assertEquals($expected, empty($actual));
        if (!empty($actual)) {
            /** @var class-string */
            $clazzName = $extension->text();
            self::assertInstanceOf($clazzName, $actual);
        } else {
            self::assertEquals($expected, in_array($extension, [ExtensionEnum::EXTENSION_MIN, ExtensionEnum::EXTENSION_ALL], true));
        }
    }

    // DataProvider

    /**
     * @return array<mixed,mixed>
     */
    public static function providerGetExtension(): array
    {
        return [
            'atlassian' => [false, ExtensionEnum::EXTENSION_ATLASSIAN],
            'admin' => [false, ExtensionEnum::EXTENSION_ATLASSIAN_ADMIN],
            'userMacro' => [false, ExtensionEnum::EXTENSION_ATLASSIAN_USER_MACRO],
            'pdt' => [false, ExtensionEnum::EXTENSION_PROJECTDOC_TOOLBOX],
            'rapClient' => [false, ExtensionEnum::EXTENSION_RAPI_CLIENT],
            'thirdParty' => [false, ExtensionEnum::EXTENSION_THIRD_PARTY],
            'all' => [true, ExtensionEnum::EXTENSION_ALL],
            'min' => [true, ExtensionEnum::EXTENSION_MIN],
        ];
    }
}
