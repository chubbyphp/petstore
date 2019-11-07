<?php

declare(strict_types=1);

namespace App;

use App\Config\DevConfig;
use App\Config\PhpunitConfig;
use App\Config\ProdConfig;
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
use Chubbyphp\ApiHttp\Provider\ApiHttpProvider;
use Chubbyphp\Config\ConfigMapping;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\Pimple\ConfigServiceProvider;
use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use Chubbyphp\DoctrineDbServiceProvider\ServiceProvider\DoctrineDbalServiceProvider;
use Chubbyphp\DoctrineDbServiceProvider\ServiceProvider\DoctrineOrmServiceProvider;
use Chubbyphp\Negotiation\Provider\NegotiationProvider;
use Chubbyphp\Serialization\Provider\SerializationProvider;
use Chubbyphp\Validation\Provider\ValidationProvider;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;

return static function (string $env) {
    $container = new Container(['env' => $env]);

    $container[PsrContainer::class] = static function () use ($container) {
        return new PsrContainer($container);
    };

    $container->register(new ApiHttpProvider());
    $container->register(new DeserializationProvider());
    $container->register(new DoctrineDbalServiceProvider());
    $container->register(new DoctrineOrmServiceProvider());
    $container->register(new MonologServiceProvider());
    $container->register(new NegotiationProvider());
    $container->register(new SerializationProvider());
    $container->register(new ValidationProvider());

    $container->register(new ApiHttpServiceProvider());
    $container->register(new DeserializationServiceProvider());
    $container->register(new DoctrineServiceProvider());
    $container->register(new FactoryServiceProvider());
    $container->register(new NegotiationServiceProvider());
    $container->register(new ProxyManagerServiceProvider());
    $container->register(new SerializationServiceProvider());
    $container->register(new RepositoryServiceProvider());
    $container->register(new ValidationServiceProvider());

    $container->register(new ConfigServiceProvider(new ConfigProvider(__DIR__.'/..', [
        new ConfigMapping('dev', DevConfig::class),
        new ConfigMapping('phpunit', PhpunitConfig::class),
        new ConfigMapping('prod', ProdConfig::class),
    ])));

    return $container;
};
