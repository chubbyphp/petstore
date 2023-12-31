<?php

declare(strict_types=1);

use App\Factory\Collection\PetCollectionFactory;
use App\Factory\Model\PetFactory;
use App\Mapping\Orm\PetMapping;
use App\Mapping\Orm\VaccinationMapping;
use App\Model\Pet;
use App\Model\Vaccination;
use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\PingRequestHandler;
use Doctrine\DBAL\Connection;
use App\RequestHandler\OpenapiRequestHandler;
use App\ServiceFactory\Command\CommandsFactory;
use App\ServiceFactory\Deserialization\DenormalizationObjectMappingsFactory;
use App\ServiceFactory\Deserialization\TypeDecodersFactory;
use App\ServiceFactory\Factory\Collection\PetCollectionFactoryFactory;
use App\ServiceFactory\Factory\Model\PetFactoryFactory;
use App\ServiceFactory\Framework\ExceptionMiddlewareFactory;
use App\ServiceFactory\Framework\MiddlewaresFactory;
use App\ServiceFactory\Framework\RouteMatcherFactory;
use App\ServiceFactory\Framework\RouteMatcherMiddlewareFactory;
use App\ServiceFactory\Framework\RoutesByNameFactory;
use App\ServiceFactory\Framework\UrlGeneratorFactory;
use App\ServiceFactory\Http\ResponseFactoryFactory;
use App\ServiceFactory\Http\StreamFactoryFactory;
use App\ServiceFactory\Logger\LoggerFactory;
use App\ServiceFactory\Negotiation\AcceptNegotiatorSupportedMediaTypesFactory;
use App\ServiceFactory\Negotiation\ContentTypeNegotiatorSupportedMediaTypesFactory;
use App\ServiceFactory\Repository\PetRepositoryFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetCreateRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetDeleteRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetListRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetReadRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\Api\Crud\PetUpdateRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\PingRequestHandlerFactory;
use App\ServiceFactory\RequestHandler\OpenapiRequestHandlerFactory;
use App\ServiceFactory\Serialization\NormalizationObjectMappingsFactory;
use App\ServiceFactory\Serialization\TypeEncodersFactory;
use App\ServiceFactory\Validation\ValidationMappingProviderFactory;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\ApiHttp\Middleware\ApiExceptionMiddleware;
use Chubbyphp\ApiHttp\ServiceFactory\AcceptAndContentTypeMiddlewareFactory;
use Chubbyphp\ApiHttp\ServiceFactory\ApiExceptionMiddlewareFactory;
use Chubbyphp\ApiHttp\ServiceFactory\RequestManagerFactory;
use Chubbyphp\ApiHttp\ServiceFactory\ResponseManagerFactory;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\ServiceFactory\CorsMiddlewareFactory;
use Chubbyphp\DecodeEncode\Decoder\TypeDecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\TypeEncoderInterface;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Deserialization\ServiceFactory\DeserializerFactory;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
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
use Chubbyphp\Negotiation\ServiceFactory\AcceptNegotiatorFactory;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeNegotiatorFactory;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Chubbyphp\Serialization\SerializerInterface;
use Chubbyphp\Serialization\ServiceFactory\SerializerFactory;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderRegistryInterface;
use Chubbyphp\Validation\ServiceFactory\ValidationMappingProviderRegistryFactory;
use Chubbyphp\Validation\ServiceFactory\ValidatorFactory;
use Chubbyphp\Validation\ValidatorInterface;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Monolog\Level;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Psr\Cache\CacheItemPoolInterface;

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
            AcceptAndContentTypeMiddleware::class => AcceptAndContentTypeMiddlewareFactory::class,
            AcceptNegotiatorInterface::class . 'supportedMediaTypes[]' => AcceptNegotiatorSupportedMediaTypesFactory::class,
            AcceptNegotiatorInterface::class => AcceptNegotiatorFactory::class,
            ApiExceptionMiddleware::class => ApiExceptionMiddlewareFactory::class,
            CacheItemPoolInterface::class => ApcuAdapterFactory::class,
            Command::class . '[]' => CommandsFactory::class,
            Connection::class => ConnectionFactory::class,
            ConnectionProvider::class => ContainerConnectionProviderFactory::class,
            ContentTypeNegotiatorInterface::class . 'supportedMediaTypes[]' => ContentTypeNegotiatorSupportedMediaTypesFactory::class,
            ContentTypeNegotiatorInterface::class => ContentTypeNegotiatorFactory::class,
            CorsMiddleware::class => CorsMiddlewareFactory::class,
            DenormalizationObjectMappingInterface::class . '[]' => DenormalizationObjectMappingsFactory::class,
            DeserializerInterface::class => DeserializerFactory::class,
            EntityManagerInterface::class => EntityManagerFactory::class,
            EntityManagerProvider::class => ContainerEntityManagerProviderFactory::class,
            ExceptionMiddleware::class => ExceptionMiddlewareFactory::class,
            LoggerInterface::class => LoggerFactory::class,
            MappingDriver::class => ClassMapDriverFactory::class,
            MiddlewareInterface::class . '[]' => MiddlewaresFactory::class,
            NormalizationObjectMappingInterface::class . '[]' => NormalizationObjectMappingsFactory::class,
            OpenapiRequestHandler::class => OpenapiRequestHandlerFactory::class,
            Pet::class . CreateRequestHandler::class => PetCreateRequestHandlerFactory::class,
            Pet::class . DeleteRequestHandler::class => PetDeleteRequestHandlerFactory::class,
            Pet::class . ListRequestHandler::class => PetListRequestHandlerFactory::class,
            Pet::class . ReadRequestHandler::class => PetReadRequestHandlerFactory::class,
            Pet::class . UpdateRequestHandler::class => PetUpdateRequestHandlerFactory::class,
            PetCollectionFactory::class => PetCollectionFactoryFactory::class,
            PetFactory::class => PetFactoryFactory::class,
            PetRepository::class => PetRepositoryFactory::class,
            PingRequestHandler::class => PingRequestHandlerFactory::class,
            RequestManagerInterface::class => RequestManagerFactory::class,
            ResponseFactoryInterface::class => ResponseFactoryFactory::class,
            ResponseManagerInterface::class => ResponseManagerFactory::class,
            RouteMatcherInterface::class => RouteMatcherFactory::class,
            RouteMatcherMiddleware::class => RouteMatcherMiddlewareFactory::class,
            RoutesByNameInterface::class => RoutesByNameFactory::class,
            SerializerInterface::class => SerializerFactory::class,
            StreamFactoryInterface::class => StreamFactoryFactory::class,
            TypeDecoderInterface::class . '[]' => TypeDecodersFactory::class,
            TypeEncoderInterface::class . '[]' => TypeEncodersFactory::class,
            UrlGeneratorInterface::class => UrlGeneratorFactory::class,
            ValidationMappingProviderInterface::class . '[]' => ValidationMappingProviderFactory::class,
            ValidationMappingProviderRegistryInterface::class => ValidationMappingProviderRegistryFactory::class,
            ValidatorInterface::class => ValidatorFactory::class,
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
