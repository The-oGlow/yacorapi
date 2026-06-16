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

namespace oglow\tools\Yacorapi\Data;

use Monolog\ConsoleLogger;
use oglow\tools\common\AbstractContainer;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\ExitCodes;
use oglow\tools\Yacorapi\IResponse;
use ollily\Tools\Emergency;
use Psr\Log\LoggerInterface;

class SpaceData extends AbstractContainer
{
    public const int SPACE_SINGLE = 1;

    public const string SPACE_SINGLE_METHOD = 'getMySpaceListSingle';

    public const int SPACE_SIMPLE = 2;

    public const string SPACE_SIMPLE_METHOD = 'getMySpaceListSimple';

    public const int SPACE_ALL = 99;

    public const string SPACE_ALL_METHOD = 'getMySpaceListAll';

    public const string MY_SPACE_NS_SEP = '\\';

    public const string MY_SPACES_NS = 'oglow\\tools\\Yacorapi';

    public const string MY_SPACES_CLAZZ = 'MySpaces';

    public const string MY_SPACES_NS_CLAZZ = self::MY_SPACE_NS_SEP . self::MY_SPACES_NS . self::MY_SPACE_NS_SEP . self::MY_SPACES_CLAZZ;

    public const string MY_SPACES_FILE = self::MY_SPACES_CLAZZ . '.php';

    private static LoggerInterface $logger;

    /** @psalm-suppress PropertyNotSetInConstructor     */
    private string $mySpaceFileDefault;

    public function __construct()
    {
        self::$logger = new ConsoleLogger(SpaceData::class);
        self::$logger->debug('START');
        parent::__construct();
        self::$logger->debug('END');
    }

    /**
     * @param array<mixed,mixed> $spaces
     *
     * @return string
     */
    public static function prepareMySpacesContent(array $spaces): string
    {
        $line = "<?php\ndeclare(strict_types=1);\n" .
            "namespace " .
            self::MY_SPACES_NS .
            ";\n" .
            "class " .
            self::MY_SPACES_CLAZZ .
            "\n{\n" .
            "public static function " .
            self::SPACE_ALL_METHOD .
            "(): array\n{return [";

        foreach ($spaces as $space) {
            $line .= sprintf("'%s',\n", $space[IResponse::KEY_KEY]);
        }

        $line .= "\n];\n}\n}\n";

        return $line;
    }

    public static function prepareMySpacesFileName(): string
    {
        return self::MY_SPACES_FILE;
    }

    public function getMySpaceFileDefault(): string
    {
        return $this->mySpaceFileDefault;
    }

    /**
     * @param string $mySpacesFile
     * @param bool   $unitTest     TRUE=don't call exit, it's an unit test (Default: FALSE)
     *
     * @return bool TRUE=file was loaded, else FALSE
     */
    public function loadPersonalSpaces(string $mySpacesFile, bool $unitTest = false): bool
    {
        $loaded = false;
        if (file_exists($mySpacesFile)) {
            $loaded = true;

            include_once $mySpacesFile; // NOSONAR: php:S4832
        } else {
            Emergency::breakSystem(ExitCodes::ERR_CODE_MYSPACES_FILE_NOT_EXISTS, sprintf('MySpaces file \'%s\' not exists!', $mySpacesFile), $unitTest);
        }

        return $loaded;
    }

    #[\Override]
    protected function prepareModes(): void
    {
        $allModes = [self::SPACE_SINGLE, self::SPACE_SIMPLE, self::SPACE_ALL];
        $this->setModes($allModes);
    }

    #[\Override]
    protected function prepareData(): void
    {
        $this->mySpaceFileDefault = ((string) $this->constData->c(ConstData::KEY_MY_DIR)) . DIRECTORY_SEPARATOR . self::MY_SPACES_FILE;

        $allData = [];
        $allData[self::SPACE_SINGLE] = $this->prepareSpaces(self::SPACE_SINGLE_METHOD, $this->mySpaceFileDefault);
        $allData[self::SPACE_SIMPLE] = $this->prepareSpaces(self::SPACE_SIMPLE_METHOD, $this->mySpaceFileDefault);
        $allData[self::SPACE_ALL] = $this->prepareSpaces(self::SPACE_ALL_METHOD, $this->mySpaceFileDefault);
        $this->setAllData($allData);
    }

    /**
     * @param string $mySpacesFunc
     * @param string $mySpaceFile
     *
     * @return mixed
     */
    private function prepareSpaces(string $mySpacesFunc, string $mySpaceFile): mixed
    {
        self::loadPersonalSpaces($mySpaceFile);
        $mySpaces = [];
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @phpstan-ignore function.impossibleType
         */
        if (method_exists(self::MY_SPACES_NS_CLAZZ, $mySpacesFunc)) {
            $refInstance = new \ReflectionClass(self::MY_SPACES_NS_CLAZZ); // @phpstan-ignore argument.type
            $refObject = new \ReflectionMethod(self::MY_SPACES_NS_CLAZZ, $mySpacesFunc);
            $mySpaces = $refObject->invoke($refInstance);
        } else {
            self::$logger->warning('MySpaces not loaded!', [$mySpacesFunc]);
        }

        return $mySpaces;
    }
}
