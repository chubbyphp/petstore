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

$app = new App($container);

$app->get('/', IndexController::class)->setName('index');
$app->group('/api', function () use ($app, $container, $acceptAndContentTypeMiddleware) {
    $app->get('', SwaggerIndexController::class)->setName('swagger_index');
    $app->get('/swagger', SwaggerYamlController::class)->setName('swagger_yml');
    $app->get('/ping', PingController::class)->add($acceptAndContentTypeMiddleware)->setName('ping');
    $app->group('/pets', function () use ($app, $container) {
        $app->get('', ListController::class.Pet::class)->setName('pet_list');
        $app->post('', CreateController::class.Pet::class)->setName('pet_create');
        $app->get('/{id}', ReadController::class.Pet::class)->setName('pet_read');
        $app->put('/{id}', UpdateController::class.Pet::class)->setName('pet_update');
        $app->delete('/{id}', DeleteController::class.Pet::class)->setName('pet_delete');
    })->add($acceptAndContentTypeMiddleware);
});

return $app;
