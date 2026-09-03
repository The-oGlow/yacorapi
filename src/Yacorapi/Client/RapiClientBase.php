<?php


namespace oglow\tools\Yacorapi\Client;

use Ds\Set;
use Monolog\ConsoleLogger;
use oglow\tools\common\IContainer;
use oglow\tools\Yacorapi\Extension\ExtensionEnum;
use oglow\tools\Yacorapi\Extension\ExtensionTrait;
use oglow\tools\Yacorapi\IConnectionProvider;
use oglow\tools\Yacorapi\IRapiClient;
use Psr\Log\LoggerInterface;

class RapiClientBase extends AbstractRapiClient implements IRapiClientBase
{
    use ExtensionTrait;
    
    private static LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function newClient(
            ?ExtensionEnum $modeExtension = IRapiClientBase::EXTENSION_DEFAULT,
            ?IConnectionProvider $connectionProvider = null,
            ?IContainer $addons = null,
            mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ): IRapiClient {
        /** @psalm-suppress PossiblyInvalidArgument
         * @phpstan-ignore argument.type */
        return new RapiClient($modeExtension, $connectionProvider, $addons, $level);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function taskitemMethods(): Set {
        return self::existingMethodNames();
    }

    /**
     * Constructor.
     *
     * @param null|ExtensionEnum              $modeExtension      (Default: {@link IRapiClientBase::EXTENSION_DEFAULT})
     * @param null|IConnectionProvider        $connectionProvider
     * @param null|IContainer                 $addons
     * @param int|\Psr\Log\LogLevel::*|string $level              The minimum logging level at which this handler will be triggered
     *                                                            (Default: {@link IRapiClientBase::LEVEL_DEFAULT})
     */
    protected function __construct(
            ?ExtensionEnum $modeExtension = IRapiClientBase::EXTENSION_DEFAULT,
            ?IConnectionProvider $connectionProvider = null,
            ?IContainer $addons = null,
            mixed $level = IRapiClientBase::LEVEL_DEFAULT
    ) {
        // Init Logger
        /** @psalm-suppress ArgumentTypeCoercion
             * @phpstan-ignore argument.type */
        self::$logger = new ConsoleLogger(name: RapiClientBase::class, level: $level);
        self::$logger->debug('START');

        parent::__construct($connectionProvider, $addons, $level);

        // Init Extensions
        if (is_null($modeExtension)) {
            $modeExtension = ExtensionEnum::EXTENSION_ALL;
        }
        $this->loadExtensions($modeExtension);

        self::$logger->debug('END');
    }
}
