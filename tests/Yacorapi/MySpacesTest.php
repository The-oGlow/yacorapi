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

namespace oglow\tools\Yacorapi;

use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\Data\SpaceData;
use oglow\tools\Yacorapi\MySpaces as PersonalSpaces;
use ollily\Tools\Reflection\UnavailableMethodsTrait;
use ollily\Tools\StopNow;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;

class MySpacesTest extends EasyGoingTestCase
{
    use UnavailableMethodsTrait;

    public const string METHOD_PREFIX    = 'getMySpaceList';

    public const array METHOD_IGNORED   = [SpaceData::SPACE_SINGLE_METHOD, SpaceData::SPACE_SIMPLE_METHOD, SpaceData::SPACE_ALL_METHOD];

    public const string METHOD_REFERENCE = SpaceData::SPACE_ALL_METHOD;

    private static LoggerInterface $logger;

    #[\Override]
    protected static function prepareO2t(): ?PersonalSpaces
    {
        try {
            static::prepareSpaceData();
            $clazzName = SpaceData::MY_SPACES_NS_CLAZZ;

            return new $clazzName();
        } catch (\Throwable $ex) {
            StopNow::stopException($ex);
        }

        return null;
    }

    #[\Override]
    protected function getCasto2t(): PersonalSpaces
    {
        return $this->o2t;
    }

    #[\Override]
    public function setUp(): void
    {
        self::$logger = new ConsoleLogger(MySpacesTest::class);
        self::$logger->debug('START');

        parent::setUp();
        self::prepareSpaceData();

        self::$logger->debug('END');
    }

    protected static function prepareSpaceData(): void
    {
        $spaceData = new SpaceData();
        $spaceData->loadPersonalSpaces($spaceData->getMySpaceFileDefault());
    }

    /**
     * @param string $prefix
     *
     * @return string[]
     */
    protected function getMethodsFiltered(string $prefix): array
    {
        $methodsFiltered = [];

        $ref        = new \ReflectionClass($this->o2t);
        $allMethods = $ref->getMethods();

        if (is_array($allMethods)) {
            foreach ($allMethods as $allMethod) {
                $methodName = $allMethod->getName();
                if (str_starts_with($methodName, $prefix)) {
                    $methodsFiltered[] = $methodName;
                }
            }
        }

        return array_unique($methodsFiltered);
    }

    /**
     * @param string[] $methodNames
     *
     * @return string[]
     */
    protected function joinResults(array $methodNames): array
    {
        $joinedResults = [];

        foreach ($methodNames as $methodName) {
            $returnValue = $this->callMethodOnO2t($methodName);
            if (is_array($returnValue)) {
                $joinedResults = array_merge($joinedResults, $returnValue);
            } else {
                self::$logger->warning('not an array', [print_r($returnValue, true)]);
            }
        }

        return array_unique($joinedResults);
    }

    public function testSpacesIntegrity(): void
    {
        $expected = $this->callMethodOnO2t(static::METHOD_REFERENCE);

        $methodsFiltered = array_diff($this->getMethodsFiltered(static::METHOD_PREFIX), static::METHOD_IGNORED);
        $actual          = $this->joinResults($methodsFiltered);
        $spacesMissing = array_values(array_diff($expected, $actual));
        if (!empty($spacesMissing)) {
            self::$logger->notice('spaceData not found:', [$spacesMissing]);
        }

        self::assertIsArray($expected);
        self::assertIsArray($actual);
        self::assertEqualsCanonicalizing($expected, $actual);
    }
}
