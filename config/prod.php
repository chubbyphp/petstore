<?php

declare(strict_types=1);

use App\Core\Middleware\ApiExceptionMiddleware;
use App\Core\RequestHandler\Api\Crud\CreateRequestHandler;
use App\Core\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\Core\RequestHandler\Api\Crud\ListRequestHandler;
use App\Core\RequestHandler\Api\Crud\ReadRequestHandler;
use App\Core\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\Core\RequestHandler\OpenapiRequestHandler;
use App\Core\RequestHandler\PingRequestHandler;
use App\Core\ServiceFactory\Command\CommandsFactory;
use App\Core\ServiceFactory\DecodeEncode\TypeDecodersFactory;
use App\Core\ServiceFactory\DecodeEncode\TypeEncodersFactory;
use App\Core\ServiceFactory\Framework\ExceptionMiddlewareFactory;
use App\Core\ServiceFactory\Framework\MiddlewaresFactory;
use App\Core\ServiceFactory\Framework\RouteMatcherFactory;
use App\Core\ServiceFactory\Framework\RouteMatcherMiddlewareFactory;
use App\Core\ServiceFactory\Framework\RoutesByNameFactory;
use App\Core\ServiceFactory\Framework\RoutesDelegator;
use App\Core\ServiceFactory\Framework\RoutesFactory;
use App\Core\ServiceFactory\Framework\UrlGeneratorFactory;
use App\Core\ServiceFactory\Http\ResponseFactoryFactory;
use App\Core\ServiceFactory\Http\StreamFactoryFactory;
use App\Core\ServiceFactory\Logger\LoggerFactory;
use App\Core\ServiceFactory\Middleware\ApiExceptionMiddlewareFactory;
use App\Core\ServiceFactory\Negotiation\AcceptNegotiatorSupportedMediaTypesFactory;
use App\Core\ServiceFactory\Negotiation\ContentTypeNegotiatorSupportedMediaTypesFactory;
use App\Core\ServiceFactory\Parsing\ParserFactory;
use App\Core\ServiceFactory\RequestHandler\OpenapiRequestHandlerFactory;
use App\Core\ServiceFactory\RequestHandler\PingRequestHandlerFactory;
use App\Pet\Model\Pet;
use App\Pet\Model\Vaccination;
use App\Pet\Orm\PetMapping;
use App\Pet\Orm\VaccinationMapping;
use App\Pet\Parsing\PetParsing;
use App\Pet\Repository\PetRepository;
use App\Pet\ServiceFactory\Framework\PetRoutesDelegator;
use App\Pet\ServiceFactory\Parsing\PetParsingFactory;
use App\Pet\ServiceFactory\Repository\PetRepositoryFactory;
use App\Pet\ServiceFactory\RequestHandler\Api\Crud\PetCreateRequestHandlerFactory;
use App\Pet\ServiceFactory\RequestHandler\Api\Crud\PetDeleteRequestHandlerFactory;
use App\Pet\ServiceFactory\RequestHandler\Api\Crud\PetListRequestHandlerFactory;
use App\Pet\ServiceFactory\RequestHandler\Api\Crud\PetReadRequestHandlerFactory;
use App\Pet\ServiceFactory\RequestHandler\Api\Crud\PetUpdateRequestHandlerFactory;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\ServiceFactory\CorsMiddlewareFactory;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\Decoder\TypeDecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\DecodeEncode\Encoder\TypeEncoderInterface;
use Chubbyphp\DecodeEncode\ServiceFactory\DecoderFactory;
use Chubbyphp\DecodeEncode\ServiceFactory\EncoderFactory;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
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
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Monolog\Level;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

$rootDir = realpath(__DIR__.'/..');
$cacheDir = $rootDir.'/var/cache/'.$env;
$logDir = $rootDir.'/var/log';

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
            AcceptNegotiatorInterface::class => AcceptNegotiatorFactory::class,
            AcceptNegotiatorInterface::class.'supportedMediaTypes[]' => AcceptNegotiatorSupportedMediaTypesFactory::class,
            ApiExceptionMiddleware::class => ApiExceptionMiddlewareFactory::class,
            CacheItemPoolInterface::class => ApcuAdapterFactory::class,
            Command::class.'[]' => CommandsFactory::class,
            Connection::class => ConnectionFactory::class,
            ConnectionProvider::class => ContainerConnectionProviderFactory::class,
            ContentTypeMiddleware::class => ContentTypeMiddlewareFactory::class,
            ContentTypeNegotiatorInterface::class => ContentTypeNegotiatorFactory::class,
            ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]' => ContentTypeNegotiatorSupportedMediaTypesFactory::class,
            CorsMiddleware::class => CorsMiddlewareFactory::class,
            DecoderInterface::class => DecoderFactory::class,
            EncoderInterface::class => EncoderFactory::class,
            EntityManagerInterface::class => EntityManagerFactory::class,
            EntityManagerProvider::class => ContainerEntityManagerProviderFactory::class,
            ExceptionMiddleware::class => ExceptionMiddlewareFactory::class,
            LoggerInterface::class => LoggerFactory::class,
            MappingDriver::class => ClassMapDriverFactory::class,
            MiddlewareInterface::class.'[]' => MiddlewaresFactory::class,
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
            ResponseFactoryInterface::class => ResponseFactoryFactory::class,
            RouteInterface::class.'[]' => RoutesFactory::class,
            RouteMatcherInterface::class => RouteMatcherFactory::class,
            RouteMatcherMiddleware::class => RouteMatcherMiddlewareFactory::class,
            RoutesByNameInterface::class => RoutesByNameFactory::class,
            StreamFactoryInterface::class => StreamFactoryFactory::class,
            TypeDecoderInterface::class.'[]' => TypeDecodersFactory::class,
            TypeEncoderInterface::class.'[]' => TypeEncodersFactory::class,
            UrlGeneratorInterface::class => UrlGeneratorFactory::class,
        ],
        'delegators' => [
            RouteInterface::class.'[]' => [
                PetRoutesDelegator::class,
                RoutesDelegator::class,
            ],
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
