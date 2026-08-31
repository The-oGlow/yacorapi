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

namespace foo;

use Monolog\ConsoleLogger;
use ollily\Tools\Reflection\UnavailableFieldsTrait;
use ollily\Tools\Reflection\UnavailableMethodsTrait;
use PHPUnit\Framework\EasyGoingTestCase;
use Psr\Log\LoggerInterface;

class FooClazzTest extends EasyGoingTestCase
{
    use UnavailableFieldsTrait;
    use UnavailableMethodsTrait;

    private static LoggerInterface $logger;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        self::$logger = new ConsoleLogger(FooClazzTest::class);
        self::$logger->debug('START');
        parent::setUpBeforeClass();
        self::$logger->debug('END');
    }

    #[\Override]
    protected static function prepareO2t(): FooClazz
    {
        return new FooClazz();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getCasto2t(): FooClazz
    {
        return $this->o2t;
    }

    public function testPrivateField(): void
    {
        $result = $this->getFieldFromO2t('privateFoo');
        // or
        $result2 = $this->getFieldByReflection(FooClazz::class, 'privateFoo', $this->o2t);
        self::$logger->info('Field privateFoo is one of ', [$result,$result2]);

        self::assertEquals('privateFooValue', $result);
        self::assertEquals($result, $result2);
    }

    public function testProtectedMethod(): void
    {
        $result = $this->callMethodOnO2t('protectedFoo');
        // or
        $result2 = $this->callMethodByReflection(FooClazz::class, 'protectedFoo', $this->o2t);
        self::$logger->info('Field protectedFoo returns ', [$result,$result2]);

        self::assertEquals('protectedFooMethod', $result);
        self::assertEquals($result, $result2);
    }

    public function testFoo(): void
    {
        self::$logger->debug('Start');

        // test code
        self::assertTrue($this->getCasto2t()->isValid());
        // or
        self::assertTrue($this->getCasto2t()->isValid());

        self::$logger->info('End');
    }
}
