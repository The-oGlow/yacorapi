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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\EasyGoingTestCase;

class ExtensionTest extends EasyGoingTestCase
{
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

    /**
     * @param ExtensionEnum $extension
     */
    #[DataProvider('providerExtensions')]
    public function testExtension(ExtensionEnum $extension): void
    {
        /** @var IExtension $newInstance */
        $newInstance = $extension->objectValue();

        self::assertInstanceOf(IExtension::class, $newInstance);
        self::assertEquals($extension->value, $newInstance::getId());
        self::assertNotEmpty($newInstance::getName());
    }

    /**
     * @return array<mixed,mixed>
     */
    public static function providerExtensions(): array
    {
        return [
            'RAPIClientExtension' => [ExtensionEnum::EXTENSION_RAPI_CLIENT],
            'AtlassianExtension'  => [  ExtensionEnum::EXTENSION_ATLASSIAN],
            'AdminExtension'      => [  ExtensionEnum::EXTENSION_ATLASSIAN_ADMIN],
            'UserMacroExtension'  => [  ExtensionEnum::EXTENSION_ATLASSIAN_USER_MACRO],
            'ThirdPartyExtension' => [  ExtensionEnum::EXTENSION_THIRD_PARTY],
            'ProjectdocExtension' => [  ExtensionEnum::EXTENSION_PROJECTDOC_TOOLBOX],
        ];
    }
}
