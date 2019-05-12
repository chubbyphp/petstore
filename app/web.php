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
use Chubbyphp\Lazy\LazyMiddleware;
use Slim\App;
use Slim\Container;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new ControllerServiceProvider());
$container->register(new MiddlewareServiceProvider());

$acceptAndContentTypeMiddleware = new LazyMiddleware($container, AcceptAndContentTypeMiddleware::class);

$web = new App($container);

$web->get('/', IndexController::class)->setName('index');
$web->group('/api', function () use ($web, $container, $acceptAndContentTypeMiddleware) {
    $web->get('', SwaggerIndexController::class)->setName('swagger_index');
    $web->get('/swagger', SwaggerYamlController::class)->setName('swagger_yml');
    $web->get('/ping', PingController::class)->add($acceptAndContentTypeMiddleware)->setName('ping');
    $web->group('/pets', function () use ($web, $container) {
        $web->get('', ListController::class.Pet::class)->setName('pet_list');
        $web->post('', CreateController::class.Pet::class)->setName('pet_create');
        $web->get('/{id}', ReadController::class.Pet::class)->setName('pet_read');
        $web->put('/{id}', UpdateController::class.Pet::class)->setName('pet_update');
        $web->delete('/{id}', DeleteController::class.Pet::class)->setName('pet_delete');
    })->add($acceptAndContentTypeMiddleware);
});

return $web;
