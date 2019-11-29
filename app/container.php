<?php

declare(strict_types=1);

namespace App;

use App\ServiceProvider\ApiHttpServiceProvider;
use App\ServiceProvider\DeserializationServiceProvider;
use App\ServiceProvider\DoctrineServiceProvider;
use App\ServiceProvider\FactoryServiceProvider;
use App\ServiceProvider\MonologServiceProvider;
use App\ServiceProvider\NegotiationServiceProvider;
use App\ServiceProvider\ProxyManagerServiceProvider;
use App\ServiceProvider\RepositoryServiceProvider;
use App\ServiceProvider\SerializationServiceProvider;
use App\ServiceProvider\ValidationServiceProvider;
use Chubbyphp\ApiHttp\Provider\ApiHttpProvider as ChubbphpApiHttpProvider;
use Chubbyphp\Deserialization\ServiceProvider\DeserializationServiceProvider as ChubbyphpDeserializationServiceProvider;
use Chubbyphp\DoctrineDbServiceProvider\ServiceProvider\DoctrineDbalServiceProvider as ChubbyphpDoctrineDbalServiceProvider;
use Chubbyphp\DoctrineDbServiceProvider\ServiceProvider\DoctrineOrmServiceProvider as ChubbyphpDoctrineOrmServiceProvider;
use Chubbyphp\Negotiation\ServiceProvider\NegotiationServiceProvider as ChubbyphpNegotiationServiceProvider;
use Chubbyphp\Serialization\ServiceProvider\SerializationServiceProvider as ChubbyphpSerializationServiceProvider;
use Chubbyphp\Validation\Provider\ValidationProvider as ChubbyphpValidationProvider;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;

return static function () {
    $container = new Container();

    $container[PsrContainer::class] = static function () use ($container) {
        return new PsrContainer($container);
    };

    $container->register(new ChubbphpApiHttpProvider());
    $container->register(new ChubbyphpDeserializationServiceProvider());
    $container->register(new ChubbyphpDoctrineDbalServiceProvider());
    $container->register(new ChubbyphpDoctrineOrmServiceProvider());
    $container->register(new ChubbyphpNegotiationServiceProvider());
    $container->register(new ChubbyphpSerializationServiceProvider());
    $container->register(new ChubbyphpValidationProvider());

    $container->register(new ApiHttpServiceProvider());
    $container->register(new DeserializationServiceProvider());
    $container->register(new DoctrineServiceProvider());
    $container->register(new FactoryServiceProvider());
    $container->register(new MonologServiceProvider());
    $container->register(new NegotiationServiceProvider());
    $container->register(new ProxyManagerServiceProvider());
    $container->register(new RepositoryServiceProvider());
    $container->register(new SerializationServiceProvider());
    $container->register(new ValidationServiceProvider());

    return $container;
};
