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

namespace oglow\tools\Yacorapi\Space;

use Monolog\ConsoleLogger;
use oglow\tools\Yacorapi\MySpaces as PersonalSpaces;
use ollily\Tools\Emergency;
use ollily\Tools\Reflection\UnavailableMethodsTrait;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;

class MySpacesTest extends EasyGoingTestCase
{
    use UnavailableMethodsTrait;

    public const string METHOD_PREFIX = 'getMySpaceList';

    /** @var array<mixed,mixed> */
    public static array $methodIgnored;

    public static string $methodReferenced;

    private static LoggerInterface $logger;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$methodReferenced = SpaceTypeEnum::SPACE_ALL->method();
        self::$methodIgnored = [SpaceTypeEnum::SPACE_SINGLE->method(), SpaceTypeEnum::SPACE_SIMPLE->method(), SpaceTypeEnum::SPACE_ALL->method()];
    }

    #[\Override]
    protected static function prepareO2t(): ?PersonalSpaces
    {
        $instance = null;

        try {
            static::prepareSpaceData();
            $clazzName = SpaceData::VAL_SPACES_CLAZZ_FULL;

            $instance = new $clazzName();
        } catch (\Throwable $ex) {
            Emergency::exceptionStop($ex);
        }

        return $instance;
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

        $ref = new \ReflectionClass($this->o2t);
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
                self::$logger->warning('This is not an array', [print_r($returnValue, true)]);
            }
        }

        return array_unique($joinedResults);
    }

    public function testSpacesIntegrity(): void
    {
        $expected = $this->callMethodOnO2t(static::$methodReferenced);

        $methodsFiltered = array_diff($this->getMethodsFiltered(static::METHOD_PREFIX), static::$methodIgnored);

        $actual = $this->joinResults($methodsFiltered);

        $spacesMissing = array_values(array_diff($expected, $actual));
        if (!empty($spacesMissing)) {
            self::$logger->notice('No spaceData found:', [$spacesMissing]);
        }

        self::assertIsArray($expected);
        self::assertIsArray($actual);
        self::assertEqualsCanonicalizing($expected, $actual);
    }
}
