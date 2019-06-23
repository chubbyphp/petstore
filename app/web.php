<?php

declare(strict_types=1);

namespace App;

use App\Controller\Crud\CreateController;
use App\Controller\Crud\DeleteController;
use App\Controller\Crud\ListController;
use App\Controller\Crud\ReadController;
use App\Controller\Crud\UpdateController;
use App\Controller\IndexController;
use App\Controller\PingController;
use App\Controller\Swagger\IndexController as SwaggerIndexController;
use App\Controller\Swagger\YamlController as SwaggerYamlController;
use App\Model\Pet;
use App\ServiceProvider\ControllerServiceProvider;
use App\ServiceProvider\MiddlewareServiceProvider;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\SlimPsr15\LazyMiddlewareAdapter;
use Chubbyphp\SlimPsr15\LazyRequestHandlerAdapter;
use Slim\App;
use Slim\Container;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new ControllerServiceProvider());
$container->register(new MiddlewareServiceProvider());

$acceptAndContentTypeMiddleware = new LazyMiddlewareAdapter($container, AcceptAndContentTypeMiddleware::class);

$web = new App($container);

$web->get('/', new LazyRequestHandlerAdapter($container, IndexController::class))->setName('index');
$web->group('/api', function () use ($web, $container, $acceptAndContentTypeMiddleware) {
    $web->get('', new LazyRequestHandlerAdapter($container, SwaggerIndexController::class))->setName('swagger_index');
    $web->get('/swagger', new LazyRequestHandlerAdapter($container, SwaggerYamlController::class))
        ->setName('swagger_yml');
    $web->get('/ping', new LazyRequestHandlerAdapter($container, PingController::class))
        ->add($acceptAndContentTypeMiddleware)
        ->setName('ping');
    $web->group('/pets', function () use ($web, $container) {
        $web->get('', new LazyRequestHandlerAdapter($container, ListController::class.Pet::class))
            ->setName('pet_list');
        $web->post('', new LazyRequestHandlerAdapter($container, CreateController::class.Pet::class))
            ->setName('pet_create');
        $web->get('/{id}', new LazyRequestHandlerAdapter($container, ReadController::class.Pet::class))
            ->setName('pet_read');
        $web->put('/{id}', new LazyRequestHandlerAdapter($container, UpdateController::class.Pet::class))
            ->setName('pet_update');
        $web->delete('/{id}', new LazyRequestHandlerAdapter($container, DeleteController::class.Pet::class))
            ->setName('pet_delete');
    })->add($acceptAndContentTypeMiddleware);
});

return $web;
