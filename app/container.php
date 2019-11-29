<?php

declare(strict_types=1);

namespace App;

use App\ServiceFactory\ApiHttpServiceFactory;
use App\ServiceFactory\DeserializationServiceFactory;
use App\ServiceFactory\DoctrineOrmServiceFactory;
use App\ServiceFactory\FactoryServiceFactory;
use App\ServiceFactory\MonologServiceFactory;
use App\ServiceFactory\NegotiationServiceFactory;
use App\ServiceFactory\ProxyManagerServiceFactory;
use App\ServiceFactory\RepositoryServiceFactory;
use App\ServiceFactory\SerializationServiceFactory;
use App\ServiceFactory\ValidationServiceFactory;
use Chubbyphp\ApiHttp\ServiceFactory\ApiHttpServiceFactory as ChubbphpApiHttpServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Deserialization\ServiceFactory\DeserializationServiceFactory as ChubbyphpDeserializationServiceFactory;
use Chubbyphp\DoctrineDbServiceProvider\ServiceFactory\DoctrineDbalServiceFactory as ChubbyphpDoctrineDbalServiceFactory;
use Chubbyphp\DoctrineDbServiceProvider\ServiceFactory\DoctrineOrmServiceFactory as ChubbyphpDoctrineOrmServiceFactory;
use Chubbyphp\Negotiation\ServiceFactory\NegotiationServiceFactory as ChubbyphpNegotiationServiceFactory;
use Chubbyphp\Serialization\ServiceFactory\SerializationServiceFactory as ChubbyphpSerializationServiceFactory;
use Chubbyphp\Validation\ServiceFactory\ValidationServiceFactory as ChubbyphpValidationServiceFactory;

return static function () {
    $container = new Container();

    $container->factories((new ChubbphpApiHttpServiceFactory())());
    $container->factories((new ChubbyphpDeserializationServiceFactory())());
    $container->factories((new ChubbyphpDoctrineDbalServiceFactory())());
    $container->factories((new ChubbyphpDoctrineOrmServiceFactory())());
    $container->factories((new ChubbyphpNegotiationServiceFactory())());
    $container->factories((new ChubbyphpSerializationServiceFactory())());
    $container->factories((new ChubbyphpValidationServiceFactory())());

    $container->factories((new ApiHttpServiceFactory())());
    $container->factories((new DeserializationServiceFactory())());
    $container->factories((new DoctrineOrmServiceFactory())());
    $container->factories((new FactoryServiceFactory())());
    $container->factories((new MonologServiceFactory())());
    $container->factories((new NegotiationServiceFactory())());
    $container->factories((new ProxyManagerServiceFactory())());
    $container->factories((new RepositoryServiceFactory())());
    $container->factories((new SerializationServiceFactory())());
    $container->factories((new ValidationServiceFactory())());

    return $container;
};
