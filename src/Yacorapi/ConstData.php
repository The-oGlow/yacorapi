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

use Ds\Map;
use Monolog\ConsoleLogger;
use Monolog\DoNothingLogger;
use oglow\tools\common\AbstractSingleton;
use oglow\tools\Yacorapi\Data\RequestParameterData;
use ollily\Tools\Emergency;
use ollily\Tools\EnvironmentVariableTrait;
use Psr\Log\LoggerInterface;

/**
 * Class ConstData.
 *
 * @SuppressWarnings("PHPMD.CamelCaseMethodName")
 */
// @phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
final class ConstData extends AbstractSingleton
{
    use EnvironmentVariableTrait;

    //
    // Public Consts
    // Page Consts
    /** First line on a page */
    public const int PAGE_START = 0;

    /** Last line on a page */
    public const int PAGE_LIMIT = 50;

    /** Max count of pages */
    public const int PAGE_MAX_PAGES = 20;

    /** Max count of lines */
    public const int PAGE_MAX_RESULTS = 50 * 20;

    // Instance Consts
    /** Key: URL of the confluence instance */
    public const string KEY_CONF_BASE_URL = 'CONF_BASE_URL';

    // Folder Consts
    /** Key: Folder where the personal information are stored */
    public const string KEY_MY_DIR = 'MY_DIR';

    /** Key: Folder of the project-root */
    public const string KEY_PROJECT_ROOT = 'PROJECT_ROOT';

    /** Key: Basefolder of the generated files */
    public const string KEY_TARGET_ROOTDIR = 'TARGET_ROOTDIR';

    /** Key: Folder for the target with the current run */
    public const string KEY_TARGET_DIR = 'TARGET_DIR';

    /** Key: Basefolder for all input files */
    public const string KEY_INPUT_ROOTDIR = 'INPUT_ROOTDIR';

    /** Key: Folder for the input files with the current run */
    public const string KEY_INPUT_DIR = 'INPUT_DIR';

    // Url Consts
    /** Key: Confluence URL for accessing the content */
    public const string KEY_CONF_CONTENT_URL = 'CONF_CONTENT_URL';

    /** Key: Confluence URL for using the search */
    public const string KEY_CONF_SEARCH_URL = 'CONF_SEARCH_URL';

    /** Key: Confluence URL for accessing space data */
    public const string KEY_CONF_SPACE_URL = 'CONF_SPACE_URL';

    // Misc Consts
    /** Key: Confluence URL for recieving the rendered page content */
    public const string KEY_WEB_SHOW_PAGEID = 'WEB_SHOW_PAGEID';

    /** Key: Currently defined max count of search results */
    public const string KEY_SEARCH_LIMIT = 'SEARCH_LIMIT';

    /** Foldername for the original recieved files */
    public const string TARGET_ORGDIR = 'org';

    /** Foldername for the modified files */
    public const string TARGET_MODDIR = 'mod';

    /** URL path for accessing the content */
    public const string C_RAPI_CONTENT = '/rest/api/content';

    /** URL path for using the search with 'scan' */
    public const string C_RAPI_SCAN = self::C_RAPI_CONTENT . '/scan';

    /** URL path for using the search with 'search' */
    public const string C_RAPI_SEARCH = '/rest/api/search';

    /** URL path for accessing space data */
    public const string C_RAPI_SPACE = '/rest/api/space';

    /** URL path for receiving the rendered page content */
    public const string C_RAPI_VIEWPAGE = '/pages/viewpage.action?pageId=';

    /** URL path for accessing page restrictions */
    public const string C_RAPI_RESTRICTION = '/restriction';

    /** URL path for accessing page restrictions by mode */
    public const string C_RAPI_RESTRICTION_BYOP = '/restriction/byOperation';

    //
    // Private Consts
    // User Configuration Consts
    /** Filename of the certificate file */
    private const string CONF_USERCERTFILE = 'cacert.pem';

    /** Filename of the authorisation class */
    private const string CONF_USERAUTHFILE = 'MyAuth.php';

    /** Foldername where the personal information are stored */
    private const string CONF_USERFOLDER = '.yacorapi';

    /** Classname of the authorisation class */
    private const string CONF_AUTH_CLAZZ = '\oglow\tools\Yacorapi\MyAuth';

    // Auth Consts
    /** Key: Name of the token the authentication is stored */
    public const string KEY_AUTH_TOKEN_NAME = 'AUTH_TOKEN_NAME';

    /** Key: Filename of the certificate file */
    public const string KEY_MY_CERT_CA = 'MY_CERT_CA';

    /** Key: Flag, which instance is used, true=production, false=test */
    public const string KEY_USE_PROD = 'USE_PROD';

    /** Key: URL of the test-instance */
    public const string KEY_TEST_URL = 'TEST_URL';

    /** Key: URL of the production-instance */
    public const string KEY_PROD_URL = 'PROD_URL';

    /** Key: Authorisation token for production instance */
    private const string KEY_CONF_PAT_PROD = 'CONF_PAT_PROD';

    /** Key: Authorisation token for test instance */
    private const string KEY_CONF_PAT_TEST = 'CONF_PAT_TEST';

    /** List of options (long) */
    private const array CLI_LONG_OPTS = [self::KEY_USE_PROD . ':'];

    private static LoggerInterface $logger;

    private static string $tsNow;

    // Variables

    /** @var Map<string,scalar> */
    private Map $definedConst;

    private object $userAuth;

    public function __construct(string $key = '', bool $withLogger = false)
    {
        // Init logger at first
        if ($withLogger) {
            self::$logger = new ConsoleLogger(ConstData::class);
        } else {
            self::$logger = new DoNothingLogger();
        }
        self::$logger->debug('START');

        // Init static vars
        self::initTsNow();
        parent::__construct($key, $withLogger);

        self::$logger->debug('END');
    }

    /**
     * @return string
     */
    public static function CONF_BASE_URL(): string // NOSONAR: php:S100
    {
        $url = '';

        if (class_exists(self::CONF_AUTH_CLAZZ)) {
            /** 
             * @psalm-suppress ArgumentTypeCoercion
             * @phpstan-ignore argument.type
             */
            $clazz = new \ReflectionClass(self::CONF_AUTH_CLAZZ);
            /** @var bool */
            $useProd =  $clazz->getConstant(self::KEY_USE_PROD);
            $url = $useProd ? $clazz->getConstant(self::KEY_PROD_URL) : $clazz->getConstant(self::KEY_TEST_URL);
        }

        return $url;
    }

    /**
     * @return string
     */
    public static function getTsNow(): string
    {
        self::initTsNow();

        return self::$tsNow;
    }

    private static function initTsNow(): void
    {
        if (empty(self::$tsNow)) {
            self::$tsNow = date('Ymd-His');
        }
    }

    /**
     * @param string $constKey
     * @param mixed  $default
     *
     * @return mixed
     *
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    public function c(string $constKey, mixed $default = null): mixed
    {
        return self::getConst($constKey, $default);
    }

    /**
     * @param string $constName
     *
     * @return bool
     */
    public function isDefined(string $constName): bool
    {
        $found = $this->definedConst->hasKey($constName);
        self::$logger->info('Const is defined', [$constName, $found]);

        return $found;
    }

    /**
     * @return object
     */
    public function getPersonalAuth(): object
    {
        return $this->userAuth;
    }

    public function prepareFinalTarget(string $pathPre, string $outputFileName, string $pathPost = ''): string
    {
        self::$logger->debug('START - pathPre,outputFileName,pathPost ', [$pathPre, $outputFileName, $pathPost]);

        $pathMid = dirname($outputFileName);
        if (!empty($pathMid)) {
            $pathMidSplit = explode(DIRECTORY_SEPARATOR, $pathMid);
            $callback = fn (string $val): string => substr($val, 0, 2);
            $pathMid = implode('-', array_map($callback, $pathMidSplit));
        }
        $fullTargetFile = $pathPre . DIRECTORY_SEPARATOR . $pathMid . DIRECTORY_SEPARATOR . basename($outputFileName);
        if (!empty($pathPost)) {
            $fullTargetFile .= DIRECTORY_SEPARATOR . $pathPost;
        }

        self::$logger->debug('END - fullTargetFile', [$fullTargetFile]);

        return $fullTargetFile;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareSettings(): void
    {
        self::$logger->debug('START');

        $this->definedConst = new Map();

        $this->putConst(self::KEY_MY_DIR, self::getHome() . DIRECTORY_SEPARATOR . self::CONF_USERFOLDER);
        $this->prepareUserAuthorization((string) $this->definedConst->get(self::KEY_MY_DIR), self::CONF_USERAUTHFILE, self::CONF_AUTH_CLAZZ);

        $this->putConst(self::KEY_CONF_BASE_URL, static::CONF_BASE_URL());
        $this->defineConsts();

        self::$logger->debug('END - Is prepared', ['true']);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function validateSettings(Map $overrideParameters): bool
    {
        self::$logger->debug('START');

        $valid1 = self::validateMandatory();
        $valid2 = self::validateForProductionUse($overrideParameters);

        self::$logger->debug('END - Is valid', [$valid1, $valid2]);

        return $valid1 && $valid2;
    }

    /**
     * @param string $authFilePath
     * @param string $authFileName
     * @param string $authClazzName
     *
     * @return bool
     */
    protected function prepareUserAuthorization(string $authFilePath, string $authFileName, string $authClazzName): bool
    {
        self::$logger->debug('START');

        $prepared = false;

        $authFile = $authFilePath . DIRECTORY_SEPARATOR . $authFileName;
        if (file_exists($authFile)) {
            include_once $authFile; // NOSONAR: php:S4832
            if (class_exists($authClazzName)) {
                $clazz = new \ReflectionClass($authClazzName);
                $this->userAuth = $clazz->newInstance();
                $prepared = true;
            } else {
                Emergency::breakSystem(ExitCodes::ERR_CODE_AUTH_CLASS_NOT_EXISTS, sprintf('User athorization not loaded: %s', $authFile));
            }
        } else {
            Emergency::breakSystem(ExitCodes::ERR_CODE_AUTHFILE_NOT_EXISTS, sprintf('User athorization not loaded: %s', $authFile));
        }

        self::$logger->debug('END - Is prepared', [$prepared]);

        return $prepared;
    }

    protected function defineConsts(): void
    {
        self::$logger->debug('START');

        // Common
        $this->putConst(self::KEY_MY_CERT_CA, ((string) self::getConst(self::KEY_MY_DIR)) . DIRECTORY_SEPARATOR . self::CONF_USERCERTFILE);
        $this->putConst(self::KEY_WEB_SHOW_PAGEID, sprintf('%s' . self::C_RAPI_VIEWPAGE, self::getConst(self::KEY_CONF_BASE_URL)));

        // Urls
        $this->putConst(self::KEY_CONF_CONTENT_URL, sprintf('%s' . self::C_RAPI_CONTENT, self::getConst(self::KEY_CONF_BASE_URL)));
        $this->putConst(self::KEY_CONF_SEARCH_URL, sprintf('%s' . self::C_RAPI_SEARCH, self::getConst(self::KEY_CONF_BASE_URL)));
        $this->putConst(self::KEY_CONF_SPACE_URL, sprintf('%s' . self::C_RAPI_SPACE, self::getConst(self::KEY_CONF_BASE_URL)));

        // Folders
        $this->putConst(self::KEY_PROJECT_ROOT, realpath(__DIR__ . str_repeat(DIRECTORY_SEPARATOR . '..', 2)));
        $this->putConst(
            self::KEY_TARGET_ROOTDIR,
            sprintf('%s%starget', self::getConst(self::KEY_PROJECT_ROOT), DIRECTORY_SEPARATOR)
        );
        $this->putConst(
            self::KEY_TARGET_DIR,
            sprintf(
                '%s%s%s',
                self::getConst(self::KEY_TARGET_ROOTDIR),
                DIRECTORY_SEPARATOR,
                '' . self::$tsNow
            )
        );
        $this->putConst(
            self::KEY_INPUT_ROOTDIR,
            sprintf('%s%sinput', self::getConst(self::KEY_PROJECT_ROOT), DIRECTORY_SEPARATOR)
        );
        $this->putConst(
            self::KEY_INPUT_DIR,
            sprintf(
                '%s',
                self::getConst(self::KEY_INPUT_ROOTDIR)
            )
        );

        self::$logger->debug('END - Is defined', ['true']);
    }

    /**
     * @param Map<mixed, mixed> $overrideParameters
     *
     * @return bool
     */
    protected function validateForProductionUse(Map $overrideParameters): bool
    {
        self::$logger->debug('START');

        $validated = true;

        $ovUseProd = $this->parseBool($overrideParameters, self::KEY_USE_PROD);
        if (!empty($ovUseProd)) {
            $ovUseProd = filter_var($ovUseProd, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }
        if (is_bool($ovUseProd)) {
            $this->putConst(self::KEY_USE_PROD, $ovUseProd);
        } else {
            if (class_exists(PersonalAuth::class)) {
                $this->putConst(self::KEY_USE_PROD, PersonalAuth::USE_PROD);
            } else {
                $this->putConst(self::KEY_USE_PROD, false);
            }
        }

        if ($this->getConst(self::KEY_USE_PROD, false) === true) {
            self::$logger->notice('+++ RUNNING ON PRODUCTION IS OK 4 U? +++');
            if (!$this->isDefined(self::KEY_AUTH_TOKEN_NAME)) {
                $this->putConst(self::KEY_AUTH_TOKEN_NAME, self::KEY_CONF_PAT_PROD);
            }
        } else {
            if (!$this->isDefined(self::KEY_AUTH_TOKEN_NAME)) {
                $this->putConst(self::KEY_AUTH_TOKEN_NAME, self::KEY_CONF_PAT_TEST);
            }
        }

        self::$logger->debug('END - Is valid', [$validated]);

        return $validated;
    }

    /**
     * @return bool
     */
    protected function validateMandatory(): bool
    {
        self::$logger->debug('START');

        $validated = true;

        if (!$this->isDefined(self::KEY_CONF_BASE_URL)) {
            $validated = true;
            Emergency::breakSystem(ExitCodes::ERR_CODE_NO_URL_SET, 'No URL for confluence is set');
        }
        if (!$this->isDefined(self::KEY_SEARCH_LIMIT)) {
            $this->putConst(self::KEY_SEARCH_LIMIT, ((string) RequestParameterData::SEARCH_LIMIT_MAX));
        }

        self::$logger->debug('END - Is valid', [$validated]);

        return $validated;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareLongOpts(): array
    {
        return self::CLI_LONG_OPTS;
    }

    /**
     * @param mixed $constName
     * @param mixed $constValue
     * @param bool  $replace    TRUE=replace constant, if already exists, else FALSE
     */
    private function putConst(mixed $constName, mixed $constValue, bool $replace = true): void
    {
        if ($this->isDefined($constName)) {
            if ($replace) {
                $this->definedConst->put($constName, $constValue);
            } else {
                self::$logger->notice("'$constName' exists and will not be replaced.");
            }
        } else {
            $this->definedConst->put($constName, $constValue);
        }
    }

    /**
     * @param string $constKey
     * @param mixed  $default
     *
     * @return mixed
     */
    private function getConst(string $constKey, mixed $default = null): mixed
    {
        return $this->definedConst->get($constKey, $default);
    }
}
