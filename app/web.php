<?php

declare(strict_types=1);

namespace App;

use App\Model\Pet;
use App\RequestHandler\Crud\CreateRequestHandler;
use App\RequestHandler\Crud\DeleteRequestHandler;
use App\RequestHandler\Crud\ListRequestHandler;
use App\RequestHandler\Crud\ReadRequestHandler;
use App\RequestHandler\Crud\UpdateRequestHandler;
use App\RequestHandler\IndexRequestHandler;
use App\RequestHandler\PingRequestHandler;
use App\RequestHandler\Swagger\IndexRequestHandler as SwaggerIndexRequestHandler;
use App\RequestHandler\Swagger\YamlRequestHandler as SwaggerYamlRequestHandler;
use App\ServiceProvider\MiddlewareServiceProvider;
use App\ServiceProvider\RequestHandlerServiceProvider;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\SlimPsr15\LazyMiddlewareAdapter;
use Chubbyphp\SlimPsr15\LazyRequestHandlerAdapter;
use Slim\App;
use Slim\Container;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new MiddlewareServiceProvider());
$container->register(new RequestHandlerServiceProvider());

$acceptAndContentTypeMiddleware = new LazyMiddlewareAdapter($container, AcceptAndContentTypeMiddleware::class);

$web = new App($container);

$web->get('/', new LazyRequestHandlerAdapter($container, IndexRequestHandler::class))->setName('index');
$web->group('/api', function () use ($web, $container, $acceptAndContentTypeMiddleware) {
    $web->get('', new LazyRequestHandlerAdapter($container, SwaggerIndexRequestHandler::class))
        ->setName('swagger_index');
    $web->get('/swagger', new LazyRequestHandlerAdapter($container, SwaggerYamlRequestHandler::class))
        ->setName('swagger_yml');
    $web->get('/ping', new LazyRequestHandlerAdapter($container, PingRequestHandler::class))
        ->add($acceptAndContentTypeMiddleware)
        ->setName('ping');
    $web->group('/pets', function () use ($web, $container) {
        $web->get('', new LazyRequestHandlerAdapter($container, ListRequestHandler::class.Pet::class))
            ->setName('pet_list');
        $web->post('', new LazyRequestHandlerAdapter($container, CreateRequestHandler::class.Pet::class))
            ->setName('pet_create');
        $web->get('/{id}', new LazyRequestHandlerAdapter($container, ReadRequestHandler::class.Pet::class))
            ->setName('pet_read');
        $web->put('/{id}', new LazyRequestHandlerAdapter($container, UpdateRequestHandler::class.Pet::class))
            ->setName('pet_update');
        $web->delete('/{id}', new LazyRequestHandlerAdapter($container, DeleteRequestHandler::class.Pet::class))
            ->setName('pet_delete');
    })->add($acceptAndContentTypeMiddleware);
});

return $web;
