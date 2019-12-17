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
use Chubbyphp\ApiHttp\ServiceFactory\ApiHttpServiceFactory as CApiHttpServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Deserialization\ServiceFactory\DeserializationServiceFactory as CDeserializationServiceFactory;
use Chubbyphp\DoctrineDbServiceProvider\ServiceFactory\DoctrineDbalServiceFactory as CDoctrineDbalServiceFactory;
use Chubbyphp\DoctrineDbServiceProvider\ServiceFactory\DoctrineOrmServiceFactory as CDoctrineOrmServiceFactory;
use Chubbyphp\Negotiation\ServiceFactory\NegotiationServiceFactory as CNegotiationServiceFactory;
use Chubbyphp\Serialization\ServiceFactory\SerializationServiceFactory as CSerializationServiceFactory;
use Chubbyphp\Validation\ServiceFactory\ValidationServiceFactory as CValidationServiceFactory;

return static function () {
    $container = new Container();

    $container->factories((new CApiHttpServiceFactory())());
    $container->factories((new CDeserializationServiceFactory())());
    $container->factories((new CDoctrineDbalServiceFactory())());
    $container->factories((new CDoctrineOrmServiceFactory())());
    $container->factories((new CNegotiationServiceFactory())());
    $container->factories((new CSerializationServiceFactory())());
    $container->factories((new CValidationServiceFactory())());

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
