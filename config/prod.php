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
use App\Pet\Odm\PetMapping;
use App\Pet\Odm\VaccinationMapping;
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
        'factories' => [
            AcceptMiddleware::class => AcceptMiddlewareFactory::class,
            AcceptNegotiatorInterface::class.'supportedMediaTypes[]' => AcceptNegotiatorSupportedMediaTypesFactory::class,
            AcceptNegotiatorInterface::class.'supportedMediaTypes[]' => AcceptNegotiatorSupportedMediaTypesFactory::class,
            AcceptNegotiatorInterface::class => AcceptNegotiatorFactory::class,
            ApiExceptionMiddleware::class => ApiExceptionMiddlewareFactory::class,
            CacheItemPoolInterface::class => ApcuAdapterFactory::class,
            Command::class.'[]' => CommandsFactory::class,
            ContentTypeMiddleware::class => ContentTypeMiddlewareFactory::class,
            ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]' => ContentTypeNegotiatorSupportedMediaTypesFactory::class,
            ContentTypeNegotiatorInterface::class => ContentTypeNegotiatorFactory::class,
            CorsMiddleware::class => CorsMiddlewareFactory::class,
            DecoderInterface::class => DecoderFactory::class,
            DocumentManager::class => DocumentManagerFactory::class,
            EncoderInterface::class => EncoderFactory::class,
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
            RouteMatcherInterface::class => RouteMatcherFactory::class,
            RouteMatcherMiddleware::class => RouteMatcherMiddlewareFactory::class,
            RoutesByNameInterface::class => RoutesByNameFactory::class,
            RouteInterface::class.'[]' => RoutesFactory::class,
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
                'uri' => getenv('MONGO_URI'),
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
                'proxyDir' => $cacheDir.'/doctrine/mongodbOdm/proxies',
                'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                'hydratorDir' => $cacheDir.'/doctrine/mongodbOdm/hydrators',
                'hydratorNamespace' => 'DoctrineMongoDBODMHydrators',
                'metadataCache' => CacheItemPoolInterface::class,
                'defaultDB' => 'petstore',
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
