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

namespace oglow\tools\Yacorapi\Client;

use Ds\Sequence;
use Ds\Vector;
use oglow\tools\Yacorapi\Provider\MockProvider;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LogLevel;

class RapiClientBaseTest extends EasyGoingTestCase
{
    public const array AVAILABLE_METHODS = [
        'newClient', 'readPageByPageId', 'readPagesByTitle', 'checkPageExists', 'scanPages', 'searchPagesWithFilter', 'countItemsinSpace', 'spaceHomepage',
        'readRestrictionsByPageId', 'writeRestrictionsByPageId', 'listSpaces', 'countMacrosInSpace', 'createPage', 'updatePage', 'createOrUpdatePage',
        'movePage', 'prepareAddonSet', 'taskitemMethods', 'getExtensionAddonMacros', 'processQueue',
    ];

    #[\Override]
    protected static function prepareO2t(): IRapiClientBase
    {
        return RapiClient::newClient(connectionProvider: new MockProvider(LogLevel::DEBUG));
    }

    #[\Override]
    protected function getCasto2t(): IRapiClientBase
    {
        return $this->o2t;
    }

    public function testRapiMethods(): void
    {
        $expected = new Vector(self::AVAILABLE_METHODS);

        $actual = $this->getCasto2t()::taskitemMethods();

        self::assertInstanceOf(Sequence::class, $actual);
        foreach ($actual->getIterator() as $item) {
            if ($expected->contains($item)) {
                $expected->remove($expected->find($item)); // @phpstan-ignore argument.type
            } else {
                $expected->push($item);
            }
        }
        self::assertTrue($expected->isEmpty(), sprintf("Forgotten: '%s'", join('()\',\'', $expected->toArray())));
    }
}
