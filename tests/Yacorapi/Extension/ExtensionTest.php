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

use oglow\tools\Addon\Atlassian\Extension\AdminExtension;
use oglow\tools\Addon\Atlassian\Extension\AtlassianExtension;
use oglow\tools\Addon\Projectdoc\Extension\ProjectdocExtension;
use oglow\tools\Addon\ThirdParty\Extension\ThirdPartyExtension;
use oglow\tools\Addon\UserMacro\Extension\UserMacroExtension;
use oglow\tools\Yacorapi\ConstData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\ConstantCheckTestCase;

class ExtensionTest extends ConstantCheckTestCase
{
    public const string CLASS_PREFIX = IExtension::class . self::C_STATIC_SEP;

    protected const int EXPECTED_CONSTANT_COUNT = 8;

    protected const bool WITH_CONST_CROSSCHECK = true;

    /**
     * @return ExtensionTestDummyClazz
     */
    #[\Override]
    protected static function prepareO2t(): ExtensionTestDummyClazz
    {
        return new ExtensionTestDummyClazz();
    }

    /**
     * @return ExtensionTestDummyClazz
     */
    #[\Override]
    protected function getCasto2t(): ExtensionTestDummyClazz
    {
        return $this->o2t;
    }

    #[\Override]
    public static function setUpBeforeClass(bool $withConstCrossCheck = self::WITH_CONST_CROSSCHECK, int $expectedConstsCount = self::EXPECTED_CONSTANT_COUNT): void
    {
        parent::setUpBeforeClass($withConstCrossCheck, $expectedConstsCount);
    }

    /**
     * @param mixed $clazz
     * @param mixed $expected
     * @param int   $extensionId
     */
    #[DataProvider('providerExtensions')]
    public function testExtension(mixed $clazz, mixed $expected, int $extensionId): void
    {
        /** @var IExtension $newInstance */
        $newInstance = new $clazz(new ConstData());

        self::assertInstanceOf($expected, $newInstance);
        self::assertEquals($extensionId, $newInstance::getId());
        self::assertNotEmpty($newInstance::getName());
    }

    /**
     * @return array<string,array<int,mixed>>
     */
    public static function providerExtensions(): array
    {
        return [
            'RAPIClientExtension' => [RapiClientExtension::class, RapiClientExtension::class, IExtension::EXTENSION_RAPI_CLIENT],
            'AtlassianExtension'  => [AtlassianExtension::class, AtlassianExtension::class, IExtension::EXTENSION_ATLASSIAN],
            'AdminExtension'      => [AdminExtension::class, AdminExtension::class, IExtension::EXTENSION_ATLASSIAN_ADMIN],
            'UserMacroExtension'  => [UserMacroExtension::class, UserMacroExtension::class, IExtension::EXTENSION_ATLASSIAN_USER_MACRO],
            'ThirdPartyExtension' => [ThirdPartyExtension::class, ThirdPartyExtension::class, IExtension::EXTENSION_THIRD_PARTY],
            'ProjectdocExtension' => [ProjectdocExtension::class, ProjectdocExtension::class, IExtension::EXTENSION_PROJECTDOC_TOOLBOX],
        ];
    }

    public function testClassConstsExists(): void
    {
        $const              = [
            self::CLASS_PREFIX . 'EXTENSION_RAPI_CLIENT',
            self::CLASS_PREFIX . 'EXTENSION_ATLASSIAN',
            self::CLASS_PREFIX . 'EXTENSION_ATLASSIAN_ADMIN',
            self::CLASS_PREFIX . 'EXTENSION_ATLASSIAN_USER_MACRO',
            self::CLASS_PREFIX . 'EXTENSION_THIRD_PARTY',
            self::CLASS_PREFIX . 'EXTENSION_PROJECTDOC_TOOLBOX',
            self::CLASS_PREFIX . 'EXTENSION_MIN',
            self::CLASS_PREFIX . 'EXTENSION_ALL',
        ];
        static::updateActualConsts($const);

        $this->verifyConstAllExists($const);
    }
}
