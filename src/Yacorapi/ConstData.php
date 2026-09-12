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

use Ds\Collection;
use Ds\Map;
use Monolog\ConsoleLogger;
use Monolog\DoNothingLogger;
use oglow\tools\common\AbstractSingleton;
use oglow\tools\Yacorapi\Request\RequestParameter;
use ollily\Tools\Emergency;
use ollily\Tools\EnvironmentHelper;
use Psr\Log\LoggerInterface;

/**
 * Main settings clazz for the application.
 *
 * @author ollily
 */
// @phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
final class ConstData extends AbstractSingleton
{
    //
    // Public Consts

    /** @var string Name of this application */
    public const string VAL_APP_USER = 'yacorapi';

    // Page Consts
    /** @var int First line on a page */
    public const int PAGE_START = 0;

    /** @var int Last line on a page */
    public const int PAGE_LIMIT = 50;

    /** @var int Max count of pages */
    public const int PAGE_MAX_PAGES = 20;

    /** @var int Max count of lines */
    public const int PAGE_MAX_RESULTS = 50 * 20;

    // Instance Consts
    /** @var string Key: URL of the confluence instance */
    public const string KEY_CONF_BASE_URL = 'CONF_BASE_URL';

    // Folder Consts
    /** @var string Key: Folder where the personal information are stored */
    public const string KEY_MY_DIR = 'MY_DIR';

    /** @var string Key: Folder of the project-root */
    public const string KEY_PROJECT_ROOT = 'PROJECT_ROOT';

    /** @var string Key: Basefolder of the generated files */
    public const string KEY_TARGET_ROOTDIR = 'TARGET_ROOTDIR';

    /** @var string Key: Folder for the target with the current run */
    public const string KEY_TARGET_DIR = 'TARGET_DIR';

    /** @var string Key: Basefolder for all input files */
    public const string KEY_INPUT_ROOTDIR = 'INPUT_ROOTDIR';

    /** @var string Key: Folder for the input files with the current run */
    public const string KEY_INPUT_DIR = 'INPUT_DIR';

    // Url Consts
    /** @var string Key: Confluence URL for accessing the content */
    public const string KEY_CONF_CONTENT_URL = 'CONF_CONTENT_URL';

    /** @var string Key: Confluence URL for using the search */
    public const string KEY_CONF_SEARCH_URL = 'CONF_SEARCH_URL';

    /** @var string Key: Confluence URL for accessing space data */
    public const string KEY_CONF_SPACE_URL = 'CONF_SPACE_URL';

    // Misc Consts
    /** @var string Key: Confluence URL for recieving the rendered page content */
    public const string KEY_WEB_SHOW_PAGEID = 'WEB_SHOW_PAGEID';

    /** @var string Key: Currently defined max count of search results */
    public const string KEY_SEARCH_LIMIT = 'SEARCH_LIMIT';

    /** @var string Foldername for the original recieved files */
    public const string TARGET_ORGDIR = 'org';

    /** @var string Foldername for the modified files */
    public const string TARGET_MODDIR = 'mod';

    /** @var string URL path for accessing the content */
    public const string C_RAPI_CONTENT = '/rest/api/content';

    /** @var string URL path for using the search with 'scan' */
    public const string C_RAPI_SCAN = self::C_RAPI_CONTENT . '/scan';

    /** @var string URL path for using the search with 'search' */
    public const string C_RAPI_SEARCH = '/rest/api/search';

    /** @var string URL path for accessing space data */
    public const string C_RAPI_SPACE = '/rest/api/space';

    /** @var string URL path for receiving the rendered page content */
    public const string C_RAPI_VIEWPAGE = '/pages/viewpage.action?pageId=';

    /** @var string URL path for accessing page restrictions */
    public const string C_RAPI_RESTRICTION = '/restriction';

    /** @var string URL path for accessing page restrictions by mode */
    public const string C_RAPI_RESTRICTION_BYOP = '/restriction/byOperation';

    //
    // Private Consts
    // User Configuration Consts
    /** @var string Filename of the certificate file */
    private const string CONF_USERCERTFILE = 'cacert.pem';

    /** @var string Filename of the authorisation class */
    private const string CONF_USERAUTHFILE = 'MyAuth.php';

    /** @var string Foldername where the personal information are stored */
    private const string CONF_USERFOLDER = '.yacorapi';

    /** @var string Classname of the authorisation class */
    private const string CONF_AUTH_CLAZZ = '\oglow\tools\Yacorapi\MyAuth';

    // Auth Consts
    /** @var string Key: Name of the token the authentication is stored */
    public const string KEY_AUTH_TOKEN_NAME = 'AUTH_TOKEN_NAME';

    /** @var string Key: Filename of the certificate file */
    public const string KEY_MY_CERT_CA = 'MY_CERT_CA';

    /** @var string Key: Flag, which instance is used, true=production, false=test */
    public const string KEY_USE_PROD = 'USE_PROD';

    /** @var string Key: URL of the test-instance */
    public const string KEY_TEST_URL = 'TEST_URL';

    /** @var string Key: URL of the production-instance */
    public const string KEY_PROD_URL = 'PROD_URL';

    /** @var string Key: Authorisation token for production instance */
    private const string KEY_CONF_PAT_PROD = 'CONF_PAT_PROD';

    /** @var string Key: Authorisation token for test instance */
    private const string KEY_CONF_PAT_TEST = 'CONF_PAT_TEST';

    /** @var array<mixed,mixed> List of options (long) */
    private const array CLI_LONG_OPTS = [self::KEY_USE_PROD . ':'];

    private static LoggerInterface $logger;

    /** @var string Timestamp of the creation of this instance */
    private static string $tsNow;

    // Variables

    /** @var Map<string,scalar> All defined settings */
    private Collection $definedConst;

    /** @var object User authorization */
    private object $userAuth;

    /**
     * Public constructor.
     *
     * @param string $key        Unique id of this singleton
     * @param bool   $withLogger TRUE=activate logging, else FALSE
     */
    public function __construct(string $key = '', bool $withLogger = true)
    {
        // Init logger at first
        if ($withLogger) {
            self::$logger = new ConsoleLogger(ConstData::class, level: self::LEVEL_DEFAULT);
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
     * Returns the base url of the REST-API endpoint.
     *
     * @param mixed $ovUseProd Overrides the flag 'USE_PROD' flag by commandline (Default: '')
     *
     * @return string base url of the REST-API endpoint
     *
     * @see ConstData::KEY_USE_PROD
     */
    public static function CONF_BASE_URL(mixed $ovUseProd = ''): string // NOSONAR: php:S100
    {
        $url = '';

        if (class_exists(self::CONF_AUTH_CLAZZ)) {
            /**
             * @psalm-suppress ArgumentTypeCoercion
             * @phpstan-ignore argument.type
             */
            $clazz = new \ReflectionClass(self::CONF_AUTH_CLAZZ);
            /** @var bool */
            $useProd = $clazz->getConstant(self::KEY_USE_PROD);
            if (is_bool($ovUseProd)) {
                $useProd = $ovUseProd;
            }
            $url = $useProd ? $clazz->getConstant(self::KEY_PROD_URL) : $clazz->getConstant(self::KEY_TEST_URL);
        }

        return $url;
    }

    /**
     * Returns the timestamp of the creation of this instance.
     *
     * @return string Timestamp
     */
    public static function getTsNow(): string
    {
        self::initTsNow();

        return self::$tsNow;
    }

    /**
     * Remember the creation of this instance.
     */
    private static function initTsNow(): void
    {
        if (empty(self::$tsNow)) {
            self::$tsNow = date('Ymd-His');
        }
    }

    /**
     * Returns the value of the setting by shorthand.
     *
     * @param string $constKey Id of the setting
     * @param mixed  $default  A default value, if {@link $constKey} does not exist (Default: null)
     *
     * @return mixed The value of settings or the default value
     *
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    public function c(string $constKey, mixed $default = null): mixed
    {
        return $this->getConst($constKey, $default);
    }

    /**
     * Checks, if the setting exists.
     *
     * @param string $constKey Id of the setting
     *
     * @return bool TRUE=setting exists, else FALSE
     */
    public function isDefined(string $constKey): bool
    {
        $found = $this->definedConst->hasKey($constKey);
        self::$logger->info('Const is defined', [$constKey, $found]);

        return $found;
    }

    /**
     * Returns the user authorization.
     *
     * @return object User authorization
     */
    public function getPersonalAuth(): object
    {
        return $this->userAuth;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function prepareSettings(Collection $overrideParameters): void
    {
        self::$logger->debug('START');

        $ovUseProd = static::parseBool($overrideParameters, self::KEY_USE_PROD);

        $this->definedConst = new Map();

        $this->putConst(self::KEY_MY_DIR, EnvironmentHelper::getHome() . DIRECTORY_SEPARATOR . self::CONF_USERFOLDER);
        $this->prepareUserAuthorization((string) $this->definedConst->get(self::KEY_MY_DIR), self::CONF_USERAUTHFILE, self::CONF_AUTH_CLAZZ);

        if (is_bool($ovUseProd)) {
            $this->putConst(self::KEY_USE_PROD, $ovUseProd);
            $this->putConst(self::KEY_CONF_BASE_URL, static::CONF_BASE_URL($ovUseProd));
        } else {
            if (class_exists(PersonalAuth::class)) {
                $this->putConst(self::KEY_USE_PROD, PersonalAuth::USE_PROD);
            } else {
                $this->putConst(self::KEY_USE_PROD, false);
            }
            $this->putConst(self::KEY_CONF_BASE_URL, static::CONF_BASE_URL());
        }
        $this->defineConsts();

        self::$logger->debug('END - Is prepared', ['true']);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function validateSettings(Collection $overrideParameters): bool
    {
        self::$logger->debug('START');

        $valid1 = self::validateMandatory();
        $valid2 = self::validateForProductionUse();

        self::$logger->debug('END - Is valid', [$valid1, $valid2]);

        return $valid1 && $valid2;
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
     * Initialize the user authorization.
     *
     * @param string $authFilePath  Path to the authorization file
     * @param string $authFileName  Name of the authorization file
     * @param string $authClazzName Clazzname of the authorization
     *
     * @return bool TRUE=initialization successful, else FALSE
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

    /**
     * Define the settings.
     */
    protected function defineConsts(): void
    {
        self::$logger->debug('START');

        // Common
        $this->putConst(self::KEY_MY_CERT_CA, ((string) $this->getConst(self::KEY_MY_DIR)) . DIRECTORY_SEPARATOR . self::CONF_USERCERTFILE);
        $this->putConst(self::KEY_WEB_SHOW_PAGEID, sprintf('%s' . self::C_RAPI_VIEWPAGE, $this->getConst(self::KEY_CONF_BASE_URL)));

        // Urls
        $this->putConst(self::KEY_CONF_CONTENT_URL, sprintf('%s' . self::C_RAPI_CONTENT, $this->getConst(self::KEY_CONF_BASE_URL)));
        $this->putConst(self::KEY_CONF_SEARCH_URL, sprintf('%s' . self::C_RAPI_SEARCH, $this->getConst(self::KEY_CONF_BASE_URL)));
        $this->putConst(self::KEY_CONF_SPACE_URL, sprintf('%s' . self::C_RAPI_SPACE, $this->getConst(self::KEY_CONF_BASE_URL)));

        // Folders
        $this->putConst(self::KEY_PROJECT_ROOT, realpath(__DIR__ . str_repeat(DIRECTORY_SEPARATOR . '..', 2)));
        $this->putConst(
            self::KEY_TARGET_ROOTDIR,
            sprintf('%s%starget', $this->getConst(self::KEY_PROJECT_ROOT), DIRECTORY_SEPARATOR)
        );
        $this->putConst(
            self::KEY_TARGET_DIR,
            sprintf(
                '%s%s%s',
                $this->getConst(self::KEY_TARGET_ROOTDIR),
                DIRECTORY_SEPARATOR,
                '' . self::$tsNow
            )
        );
        $this->putConst(
            self::KEY_INPUT_ROOTDIR,
            sprintf('%s%sinput', $this->getConst(self::KEY_PROJECT_ROOT), DIRECTORY_SEPARATOR)
        );
        $this->putConst(
            self::KEY_INPUT_DIR,
            sprintf(
                '%s',
                $this->getConst(self::KEY_INPUT_ROOTDIR)
            )
        );

        self::$logger->debug('END - Is defined', ['true']);
    }

    /**
     * Check, if setting for production is activated.
     *
     * @return bool TRUE=production is activated, else FALSE
     */
    protected function validateForProductionUse(): bool
    {
        self::$logger->debug('START');

        $validated = true;

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
     * Checks, if mandatory settings are valid.
     *
     * @return bool TRUE=mandatory settings are valid, else FALSE
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
            $this->putConst(self::KEY_SEARCH_LIMIT, ((string) RequestParameter::VAL_SEARCH_LIMIT_MAX));
        }

        self::$logger->debug('END - Is valid', [$validated]);

        return $validated;
    }

    /**
     * Returns the value of a setting.
     *
     * @param string $constKey Id of the setting
     * @param mixed  $default  A default value, if {@link $constKey} does not exist (Default: null)
     *
     * @return mixed The value of settings or the default value
     *
     * @internal Use only for internal
     */
    private function getConst(string $constKey, mixed $default = null): mixed
    {
        return $this->definedConst->get($constKey, $default);
    }

    /**
     * Sets the value for a setting.
     *
     * @param string $constKey Id of the setting
     * @param mixed  $newValue The new value of the setting
     * @param bool   $replace  TRUE=replace the old value, if setting already exists, else FALSE
     *
     * @internal Use only for internal
     */
    private function putConst(mixed $constKey, mixed $newValue, bool $replace = true): void
    {
        if ($this->isDefined($constKey)) {
            if ($replace) {
                $this->definedConst->put($constKey, $newValue);
            } else {
                self::$logger->notice("'$constKey' exists and will not be replaced.");
            }
        } else {
            $this->definedConst->put($constKey, $newValue);
        }
    }
}
