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
use oglow\tools\common\AbstractContainer;
use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\ExitCodes;
use oglow\tools\Yacorapi\IResponse;
use ollily\Tools\Emergency;
use Psr\Log\LoggerInterface;

class SpaceData extends AbstractContainer
{
    public const string VAL_SPACE_NS_SEP = '\\';

    public const string VAL_SPACES_NS = 'oglow\\tools\\Yacorapi';

    public const string VAL_SPACES_CLAZZ = 'MySpaces';

    public const string VAL_SPACES_CLAZZ_FULL = self::VAL_SPACE_NS_SEP . self::VAL_SPACES_NS . self::VAL_SPACE_NS_SEP . self::VAL_SPACES_CLAZZ;

    public const string VAL_SPACES_FILE = self::VAL_SPACES_CLAZZ . '.php';

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
            self::VAL_SPACES_NS .
            ";\n" .
            "class " .
            self::VAL_SPACES_CLAZZ .
            "\n{\n" .
            "public static function " .
            SpaceTypeEnum::SPACE_ALL->method() .
            "(): array\n{return [";

        foreach ($spaces as $space) {
            $line .= sprintf("'%s',\n", $space[IResponse::KEY_KEY]);
        }

        $line .= "\n];\n}\n}\n";

        return $line;
    }

    public static function prepareMySpacesFileName(): string
    {
        return self::VAL_SPACES_FILE;
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
            Emergency::breakSystem(ExitCodes::ERR_CODE_MYSPACES_FILE_NOT_EXISTS, sprintf("MySpaces file '%s' does not exist", $mySpacesFile), $unitTest);
        }

        return $loaded;
    }

    #[\Override]
    protected function prepareModes(): void
    {
        $allModes = [SpaceTypeEnum::SPACE_SINGLE->value, SpaceTypeEnum::SPACE_SIMPLE->value, SpaceTypeEnum::SPACE_ALL->value];
        $this->setModes($allModes);
    }

    #[\Override]
    protected function prepareData(): void
    {
        $this->mySpaceFileDefault = ((string) $this->constData->c(ConstData::KEY_MY_DIR)) . DIRECTORY_SEPARATOR . self::VAL_SPACES_FILE;

        $allData = [];
        $allData[SpaceTypeEnum::SPACE_SINGLE->value] = $this->prepareSpaces(SpaceTypeEnum::SPACE_SINGLE->method(), $this->mySpaceFileDefault);
        $allData[SpaceTypeEnum::SPACE_SIMPLE->value] = $this->prepareSpaces(SpaceTypeEnum::SPACE_SIMPLE->method(), $this->mySpaceFileDefault);
        $allData[SpaceTypeEnum::SPACE_ALL->value] = $this->prepareSpaces(SpaceTypeEnum::SPACE_ALL->method(), $this->mySpaceFileDefault);
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
        if (method_exists(self::VAL_SPACES_CLAZZ_FULL, $mySpacesFunc)) {
            $refInstance = new \ReflectionClass(self::VAL_SPACES_CLAZZ_FULL); // @phpstan-ignore argument.type
            $refObject = new \ReflectionMethod(self::VAL_SPACES_CLAZZ_FULL, $mySpacesFunc);
            $mySpaces = $refObject->invoke($refInstance);
        } else {
            self::$logger->warning("MySpaces not loaded", [$mySpacesFunc]);
        }

        return $mySpaces;
    }
}
