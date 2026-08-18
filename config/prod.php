<?php

declare(strict_types=1);

use App\Core\RequestHandler\OpenapiRequestHandler;
use App\Core\RequestHandler\PingRequestHandler;
use App\Core\ServiceFactory\Command\CommandsFactory;
use App\Core\ServiceFactory\Framework\ErrorHandlerFactory;
use App\Core\ServiceFactory\Framework\FastRouteRouterFactory;
use App\Core\ServiceFactory\Framework\NotFoundHandlerFactory;
use App\Core\ServiceFactory\Framework\ServerRequestErrorResponseGeneratorFactory;
use App\Core\ServiceFactory\Framework\ServerRequestFactory;
use App\Core\ServiceFactory\Http\HttpClientFactory;
use App\Core\ServiceFactory\Http\RequestFactoryFactory;
use App\Core\ServiceFactory\Http\ResponseFactoryFactory;
use App\Core\ServiceFactory\Http\StreamFactoryFactory;
use App\Core\ServiceFactory\Logger\LoggerFactory;
use App\Core\ServiceFactory\RequestHandler\OpenapiRequestHandlerFactory;
use App\Core\ServiceFactory\RequestHandler\PingRequestHandlerFactory;
use App\Pet\Model\Pet;
use App\Pet\Model\Vaccination;
use App\Pet\Orm\PetMapping;
use App\Pet\Orm\VaccinationMapping;
use App\Pet\Parsing\PetParsing;
use App\Pet\Repository\PetRepository;
use App\Pet\ServiceFactory\Parsing\PetParsingFactory;
use App\Pet\ServiceFactory\Repository\PetRepositoryFactory;
use App\Pet\ServiceFactory\RequestHandler\PetCreateRequestHandlerFactory;
use App\Pet\ServiceFactory\RequestHandler\PetDeleteRequestHandlerFactory;
use App\Pet\ServiceFactory\RequestHandler\PetListRequestHandlerFactory;
use App\Pet\ServiceFactory\RequestHandler\PetReadRequestHandlerFactory;
use App\Pet\ServiceFactory\RequestHandler\PetUpdateRequestHandlerFactory;
use Chubbyphp\Api\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Api\RequestHandler\CreateRequestHandler;
use Chubbyphp\Api\RequestHandler\DeleteRequestHandler;
use Chubbyphp\Api\RequestHandler\ListRequestHandler;
use Chubbyphp\Api\RequestHandler\ReadRequestHandler;
use Chubbyphp\Api\RequestHandler\UpdateRequestHandler;
use Chubbyphp\Api\ServiceFactory\Middleware\ApiExceptionMiddlewareFactory;
use Chubbyphp\Api\ServiceFactory\Negotiation\AcceptNegotiatorSupportedMediaTypesFactory;
use Chubbyphp\Api\ServiceFactory\Negotiation\ContentTypeNegotiatorSupportedMediaTypesFactory;
use Chubbyphp\Api\ServiceFactory\Parsing\ParserFactory;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\ServiceFactory\CorsMiddlewareFactory;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\Decoder\TypeDecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\DecodeEncode\Encoder\TypeEncoderInterface;
use Chubbyphp\DecodeEncode\ServiceFactory\DecoderFactory;
use Chubbyphp\DecodeEncode\ServiceFactory\EncoderFactory;
use Chubbyphp\DecodeEncode\ServiceFactory\TypeDecodersFactory;
use Chubbyphp\DecodeEncode\ServiceFactory\TypeEncodersFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ApcuAdapterFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConnectionFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\ContainerConnectionProviderFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\ContainerEntityManagerProviderFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\AcceptMiddlewareFactory;
use Chubbyphp\Negotiation\ServiceFactory\AcceptNegotiatorFactory;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeMiddlewareFactory;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeNegotiatorFactory;
use Chubbyphp\Oidc\Middleware\OidcAuthenticationMiddleware;
use Chubbyphp\Oidc\ServiceFactory\OidcAuthenticationMiddlewareFactory;
use Chubbyphp\Parsing\ParserInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Container\ApplicationPipelineFactory;
use Mezzio\Container\EmitterFactory;
use Mezzio\Container\MiddlewareContainerFactory;
use Mezzio\Container\MiddlewareFactoryFactory;
use Mezzio\Container\RequestHandlerRunnerFactory;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\MiddlewareContainer;
use Mezzio\MiddlewareFactory;
use Mezzio\Response\ServerRequestErrorResponseGenerator;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\DispatchMiddlewareFactory;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddlewareFactory;
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Router\Middleware\RouteMiddlewareFactory;
use Mezzio\Router\RouteCollector;
use Mezzio\Router\RouteCollectorFactory;
use Mezzio\Router\RouterInterface;
use Monolog\Level;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

$rootDir = realpath(__DIR__.'/..');
$cacheDir = $rootDir.'/var/cache/'.$env;
$logDir = $rootDir.'/var/log';

return [
    'chubbyphp' => [
        'cors' => [
            'allowCredentials' => false,
            'allowHeaders' => ['Accept', 'Authorization', 'Content-Type'],
            'allowMethods' => ['DELETE', 'GET', 'POST', 'PUT'],
            'allowOrigins' => [],
            // let a browser based frontend read the bearer challenge, to distinguish a missing (no error) from an
            // invalid, e.g. expired, token (error="invalid_token"), the concrete reason intentionally does not get
            // reflected
            'exposeHeaders' => ['WWW-Authenticate'],
            'maxAge' => 7200,
        ],
        'oidc' => [
            'issuer' => getenv('OIDC_ISSUER'),
            'audience' => getenv('OIDC_AUDIENCE'),
            'realm' => 'petstore',
            // keycloak signs access tokens with RS256 by default
            'algorithms' => ['RS256'],
            // a plain http issuer (local development, ci) has to be a deliberate decision
            'allowInsecureIssuer' => 'true' === getenv('OIDC_ALLOW_INSECURE_ISSUER'),
        ],
    ],
    'debug' => false,
    'dependencies' => [
        'aliases' => [
            EntityManager::class => EntityManagerInterface::class,
        ],
        'factories' => [
            'Mezzio\ApplicationPipeline' => ApplicationPipelineFactory::class,
            AcceptMiddleware::class => AcceptMiddlewareFactory::class,
            AcceptNegotiatorInterface::class.'supportedMediaTypes[]' => AcceptNegotiatorSupportedMediaTypesFactory::class,
            AcceptNegotiatorInterface::class => AcceptNegotiatorFactory::class,
            ApiExceptionMiddleware::class => ApiExceptionMiddlewareFactory::class,
            CacheItemPoolInterface::class => ApcuAdapterFactory::class,
            ClientInterface::class => HttpClientFactory::class,
            Command::class.'[]' => CommandsFactory::class,
            Connection::class => ConnectionFactory::class,
            ConnectionProvider::class => ContainerConnectionProviderFactory::class,
            ContentTypeMiddleware::class => ContentTypeMiddlewareFactory::class,
            ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]' => ContentTypeNegotiatorSupportedMediaTypesFactory::class,
            ContentTypeNegotiatorInterface::class => ContentTypeNegotiatorFactory::class,
            CorsMiddleware::class => CorsMiddlewareFactory::class,
            DecoderInterface::class => DecoderFactory::class,
            DispatchMiddleware::class => DispatchMiddlewareFactory::class,
            EmitterInterface::class => EmitterFactory::class,
            EncoderInterface::class => EncoderFactory::class,
            EntityManagerInterface::class => EntityManagerFactory::class,
            EntityManagerProvider::class => ContainerEntityManagerProviderFactory::class,
            ErrorHandler::class => ErrorHandlerFactory::class,
            LoggerInterface::class => LoggerFactory::class,
            MappingDriver::class => ClassMapDriverFactory::class,
            MethodNotAllowedMiddleware::class => MethodNotAllowedMiddlewareFactory::class,
            MiddlewareContainer::class => MiddlewareContainerFactory::class,
            MiddlewareFactory::class => MiddlewareFactoryFactory::class,
            NotFoundHandler::class => NotFoundHandlerFactory::class,
            OidcAuthenticationMiddleware::class => OidcAuthenticationMiddlewareFactory::class,
            OpenapiRequestHandler::class => OpenapiRequestHandlerFactory::class,
            ParserInterface::class => ParserFactory::class,
            Pet::class.CreateRequestHandler::class => PetCreateRequestHandlerFactory::class,
            Pet::class.DeleteRequestHandler::class => PetDeleteRequestHandlerFactory::class,
            Pet::class.ListRequestHandler::class => PetListRequestHandlerFactory::class,
            Pet::class.ReadRequestHandler::class => PetReadRequestHandlerFactory::class,
            Pet::class.UpdateRequestHandler::class => PetUpdateRequestHandlerFactory::class,
            PetParsing::class => PetParsingFactory::class,
            PetRepository::class => PetRepositoryFactory::class,
            PingRequestHandler::class => PingRequestHandlerFactory::class,
            RequestFactoryInterface::class => RequestFactoryFactory::class,
            RequestHandlerRunner::class => RequestHandlerRunnerFactory::class,
            ResponseFactoryInterface::class => ResponseFactoryFactory::class,
            RouteCollector::class => RouteCollectorFactory::class,
            RouteMiddleware::class => RouteMiddlewareFactory::class,
            RouterInterface::class => FastRouteRouterFactory::class,
            ServerRequestErrorResponseGenerator::class => ServerRequestErrorResponseGeneratorFactory::class,
            ServerRequestInterface::class => ServerRequestFactory::class,
            StreamFactoryInterface::class => StreamFactoryFactory::class,
            TypeDecoderInterface::class.'[]' => TypeDecodersFactory::class,
            TypeEncoderInterface::class.'[]' => TypeEncodersFactory::class,
        ],
    ],
    'directories' => [
        'cache' => $cacheDir,
        'log' => $logDir,
    ],
    'doctrine' => [
        'cache' => [
            'apcu' => [
                'namespace' => 'doctrine',
            ],
        ],
        'dbal' => [
            'connection' => (new DsnParser(['pgsql' => 'pdo_pgsql']))->parse(getenv('POSTGRES_URI')),
        ],
        'driver' => [
            'classMap' => [
                'map' => [
                    Pet::class => PetMapping::class,
                    Vaccination::class => VaccinationMapping::class,
                ],
            ],
        ],
        'orm' => [
            'configuration' => [
                'metadataDriverImpl' => MappingDriver::class,
                'proxyDir' => $cacheDir.'/doctrine/orm/proxies',
                'proxyNamespace' => 'DoctrineORMProxy',
                'metadataCache' => CacheItemPoolInterface::class,
            ],
        ],
    ],
    'fastroute' => [
        'cache' => $cacheDir.'/router-cache.php',
    ],
    'monolog' => [
        'name' => 'petstore',
        'path' => $logDir.'/application.log',
        'level' => Level::Notice,
    ],
];
