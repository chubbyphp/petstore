<?php

declare(strict_types=1);

use App\Middleware\ApiExceptionMiddleware;
use App\Model\Pet;
use App\Model\Vaccination;
use App\Odm\PetMapping;
use App\Odm\VaccinationMapping;
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
use App\ServiceFactory\Framework\ExceptionMiddlewareFactory;
use App\ServiceFactory\Framework\MiddlewaresFactory;
use App\ServiceFactory\Framework\RouteMatcherFactory;
use App\ServiceFactory\Framework\RouteMatcherMiddlewareFactory;
use App\ServiceFactory\Framework\RoutesByNameFactory;
use App\ServiceFactory\Framework\UrlGeneratorFactory;
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
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ApcuAdapterFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\DocumentManagerFactory;
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
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Monolog\Level;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
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
        'factories' => [
            AcceptMiddleware::class => AcceptMiddlewareFactory::class,
            AcceptNegotiatorInterface::class . 'supportedMediaTypes[]' => AcceptNegotiatorSupportedMediaTypesFactory::class,
            AcceptNegotiatorInterface::class . 'supportedMediaTypes[]' => AcceptNegotiatorSupportedMediaTypesFactory::class,
            AcceptNegotiatorInterface::class => AcceptNegotiatorFactory::class,
            ApiExceptionMiddleware::class => ApiExceptionMiddlewareFactory::class,
            CacheItemPoolInterface::class => ApcuAdapterFactory::class,
            Command::class . '[]' => CommandsFactory::class,
            ContentTypeMiddleware::class => ContentTypeMiddlewareFactory::class,
            ContentTypeNegotiatorInterface::class . 'supportedMediaTypes[]' => ContentTypeNegotiatorSupportedMediaTypesFactory::class,
            ContentTypeNegotiatorInterface::class => ContentTypeNegotiatorFactory::class,
            CorsMiddleware::class => CorsMiddlewareFactory::class,
            DecoderInterface::class => DecoderFactory::class,
            DocumentManager::class => DocumentManagerFactory::class,
            EncoderInterface::class => EncoderFactory::class,
            ExceptionMiddleware::class => ExceptionMiddlewareFactory::class,
            LoggerInterface::class => LoggerFactory::class,
            MappingDriver::class => ClassMapDriverFactory::class,
            MiddlewareInterface::class . '[]' => MiddlewaresFactory::class,
            OpenapiRequestHandler::class => OpenapiRequestHandlerFactory::class,
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
            ResponseFactoryInterface::class => ResponseFactoryFactory::class,
            RouteMatcherInterface::class => RouteMatcherFactory::class,
            RouteMatcherMiddleware::class => RouteMatcherMiddlewareFactory::class,
            RoutesByNameInterface::class => RoutesByNameFactory::class,
            StreamFactoryInterface::class => StreamFactoryFactory::class,
            TypeDecoderInterface::class . '[]' => TypeDecodersFactory::class,
            TypeEncoderInterface::class . '[]' => TypeEncodersFactory::class,
            UrlGeneratorInterface::class => UrlGeneratorFactory::class,
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
        'driver' => [
            'classMap' => [
                'map' => [
                    Pet::class => PetMapping::class,
                    Vaccination::class => VaccinationMapping::class,
                ],
            ],
        ],
        'mongodb' => [
            'client' => [
                'uri' => getenv('DATABASE_URI'),
                'driverOptions' => [
                    'typeMap' => DocumentManager::CLIENT_TYPEMAP,
                    'driver' => [
                        'name' => 'doctrine-odm',
                    ],
                ],
            ],
        ],
        'mongodbOdm' => [
            'configuration' => [
                'metadataDriverImpl' => MappingDriver::class,
                'proxyDir' => $cacheDir . '/doctrine/mongodbOdm/proxies',
                'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                'hydratorDir' => $cacheDir . '/doctrine/mongodbOdm/hydrators',
                'hydratorNamespace' => 'DoctrineMongoDBODMHydrators',
                'metadataCache' => CacheItemPoolInterface::class,
                'defaultDB' => 'petstore',
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
