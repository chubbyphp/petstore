<?php

declare(strict_types=1);

use App\Middleware\ApiExceptionMiddleware;
use App\Model\Pet;
use App\Model\Vaccination;
use App\Orm\PetMapping;
use App\Orm\VaccinationMapping;
use App\Parsing\PetParsing;
use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\OpenapiRequestHandler;
use App\RequestHandler\PingRequestHandler;
use App\ServiceFactory\Command\CommandsFactory;
use App\ServiceFactory\DecodeEncode\TypeDecodersFactory;
use App\ServiceFactory\DecodeEncode\TypeEncodersFactory;
use App\ServiceFactory\Framework\ErrorHandlerFactory;
use App\ServiceFactory\Framework\FastRouteRouterFactory;
use App\ServiceFactory\Framework\NotFoundHandlerFactory;
use App\ServiceFactory\Framework\ResponseFactory;
use App\ServiceFactory\Framework\ServerRequestErrorResponseGeneratorFactory;
use App\ServiceFactory\Framework\ServerRequestFactory;
use App\ServiceFactory\Http\ResponseFactoryFactory;
use App\ServiceFactory\Http\StreamFactoryFactory;
use App\ServiceFactory\Logger\LoggerFactory;
use App\ServiceFactory\Middleware\ApiExceptionMiddlewareFactory;
use App\ServiceFactory\Negotiation\AcceptNegotiatorSupportedMediaTypesFactory;
use App\ServiceFactory\Negotiation\ContentTypeNegotiatorSupportedMediaTypesFactory;
use App\ServiceFactory\Parsing\ParserFactory;
use App\ServiceFactory\Parsing\PetParsingFactory;
use App\ServiceFactory\Repository\PetRepositoryFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetCreateRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetDeleteRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetListRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetReadRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetUpdateRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\OpenapiRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\PingRequestHandlerFactory;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\ServiceFactory\CorsMiddlewareFactory;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\Decoder\TypeDecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\DecodeEncode\Encoder\TypeEncoderInterface;
use Chubbyphp\DecodeEncode\ServiceFactory\DecoderFactory;
use Chubbyphp\DecodeEncode\ServiceFactory\EncoderFactory;
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
use Chubbyphp\Parsing\ParserInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\ApplicationPipeline;
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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

$rootDir = \realpath(__DIR__ . '/..');
$cacheDir = $rootDir . '/var/cache/' . $env;
$logDir = $rootDir . '/var/log';

return [
    'chubbyphp' => [
        'cors' => [
            'allowCredentials' => false,
            'allowHeaders' => ['Accept', 'Content-Type'],
            'allowMethods' => ['DELETE', 'GET', 'POST', 'PUT'],
            'allowOrigins' => [],
            'exposeHeaders' => [],
            'maxAge' => 7200,
        ],
    ],
    'debug' => false,
    'dependencies' => [
        'aliases' => [
            EntityManager::class => EntityManagerInterface::class,
        ],
        'factories' => [
            AcceptMiddleware::class => AcceptMiddlewareFactory::class,
            AcceptNegotiatorInterface::class . 'supportedMediaTypes[]' => AcceptNegotiatorSupportedMediaTypesFactory::class,
            AcceptNegotiatorInterface::class => AcceptNegotiatorFactory::class,
            ApiExceptionMiddleware::class => ApiExceptionMiddlewareFactory::class,
            ApplicationPipeline::class => ApplicationPipelineFactory::class,
            CacheItemPoolInterface::class => ApcuAdapterFactory::class,
            Command::class . '[]' => CommandsFactory::class,
            Connection::class => ConnectionFactory::class,
            ConnectionProvider::class => ContainerConnectionProviderFactory::class,
            ContentTypeMiddleware::class => ContentTypeMiddlewareFactory::class,
            ContentTypeNegotiatorInterface::class . 'supportedMediaTypes[]' => ContentTypeNegotiatorSupportedMediaTypesFactory::class,
            ContentTypeNegotiatorInterface::class => ContentTypeNegotiatorFactory::class,
            CorsMiddleware::class => CorsMiddlewareFactory::class,
            DispatchMiddleware::class => DispatchMiddlewareFactory::class,
            EmitterInterface::class => EmitterFactory::class,
            DecoderInterface::class => DecoderFactory::class,
            EncoderInterface::class => EncoderFactory::class,
            EntityManagerInterface::class => EntityManagerFactory::class,
            EntityManagerProvider::class => ContainerEntityManagerProviderFactory::class,
            ErrorHandler::class => ErrorHandlerFactory::class,
            LoggerInterface::class => LoggerFactory::class,
            MappingDriver::class => ClassMapDriverFactory::class,
            MethodNotAllowedMiddleware::class => MethodNotAllowedMiddlewareFactory::class,
            MiddlewareContainer::class => MiddlewareContainerFactory::class,
            MiddlewareFactory::class => MiddlewareFactoryFactory::class,
            MiddlewareFactory::class => MiddlewareFactoryFactory::class,
            NotFoundHandler::class => NotFoundHandlerFactory::class,
            OpenapiRequestHandler::class => OpenapiRequestHandlerFactory::class,
            ParserInterface::class => ParserFactory::class,
            Pet::class . CreateRequestHandler::class => PetCreateRequestHandlerFactory::class,
            Pet::class . DeleteRequestHandler::class => PetDeleteRequestHandlerFactory::class,
            Pet::class . ListRequestHandler::class => PetListRequestHandlerFactory::class,
            Pet::class . ReadRequestHandler::class => PetReadRequestHandlerFactory::class,
            Pet::class . UpdateRequestHandler::class => PetUpdateRequestHandlerFactory::class,
            PetParsing::class => PetParsingFactory::class,
            PetRepository::class => PetRepositoryFactory::class,
            PingRequestHandler::class => PingRequestHandlerFactory::class,
            RequestHandlerRunner::class => RequestHandlerRunnerFactory::class,
            ResponseFactoryInterface::class => ResponseFactoryFactory::class,
            ResponseInterface::class => ResponseFactory::class,
            RouteCollector::class => RouteCollectorFactory::class,
            RouteMiddleware::class => RouteMiddlewareFactory::class,
            RouterInterface::class => FastRouteRouterFactory::class,
            ServerRequestErrorResponseGenerator::class => ServerRequestErrorResponseGeneratorFactory::class,
            ServerRequestInterface::class => ServerRequestFactory::class,
            StreamFactoryInterface::class => StreamFactoryFactory::class,
            TypeDecoderInterface::class . '[]' => TypeDecodersFactory::class,
            TypeEncoderInterface::class . '[]' => TypeEncodersFactory::class,
            ResponseFactoryInterface::class => ResponseFactoryFactory::class,
            StreamFactoryInterface::class => StreamFactoryFactory::class,
            TypeDecoderInterface::class . '[]' => TypeDecodersFactory::class,
            TypeEncoderInterface::class . '[]' => TypeEncodersFactory::class,
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
            'connection' => [
                'driver' => 'pdo_pgsql',
                'charset' => 'utf8',
                'user' => getenv('DATABASE_USER'),
                'password' => getenv('DATABASE_PASS'),
                'host' => getenv('DATABASE_HOST'),
                'port' => getenv('DATABASE_PORT'),
                'dbname' => getenv('DATABASE_NAME'),
            ],
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
                'proxyDir' => $cacheDir . '/doctrine/orm/proxies',
                'proxyNamespace' => 'DoctrineORMProxy',
                'metadataCache' => CacheItemPoolInterface::class,
            ],
        ],
    ],
    'fastroute' => [
        'cache' => $cacheDir . '/router-cache.php',
    ],
    'monolog' => [
        'name' => 'petstore',
        'path' => $logDir . '/' . $env . '.log',
        'level' => Level::Notice,
    ],
];
