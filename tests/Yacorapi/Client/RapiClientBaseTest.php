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

use Ds\Set;
use Monolog\ConsoleLogger;
use oglow\tools\common\MockProvider;
use oglow\tools\Yacorapi\Client\IRapiClientBase;
use oglow\tools\Yacorapi\Response\ResponseParameterData;
use oglow\tools\Yacorapi\Statistic\StatisticStatistic;
use oglow\tools\Yacorapi\Statistic\StatisticTypeEnum;
use oglow\tools\Yacorapi\YacorapiTestData;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use oglow\tools\Yacorapi\Client\RapiClient;

class RapiClientBaseTest extends EasyGoingTestCase
{
    public const array AVAILABLE_METHODS = [
        'newClient', 'readPageByPageId', 'readPagesByTitle', 'checkPageExists', 'scanPages', 'searchPagesWithFilter', 'countItemsinSpace', 'spaceHomepage',
        'readRestrictionsByPageId', 'writeRestrictionsByPageId', 'listSpaces', 'countMacrosInSpace', 'createPage', 'updatePage', 'createOrUpdatePage',
             'movePage', 'prepareAddonSet', 'taskitemMethods', 'getExtensionAddonMacros', 'processQueue',
    ];


    private static LoggerInterface $logger;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$logger = new ConsoleLogger(RapiClientBaseTest::class);
    }

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
        $expected = new Set(self::AVAILABLE_METHODS);

        /** @var Set<non-empty-string> */
        $actual = $this->getCasto2t()::taskitemMethods();

        self::assertInstanceOf(Set::class, $actual);
        foreach ($actual->getIterator() as $item) {
            if ($expected->contains($item)) {
                $expected->remove($item);
            } else {
                $expected->add($item);
            }
        }
        self::assertTrue($expected->isEmpty(), sprintf("Forgotten: '%s'", join('()\',\'', $expected->toArray())));
    }

}
