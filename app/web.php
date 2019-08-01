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
use App\ServiceProvider\SlimServiceProvider;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Slim\App;
use Slim\CallableResolver;
use Slim\Routing\RouteCollector;
use Slim\Routing\RouteCollectorProxy;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new MiddlewareServiceProvider());
$container->register(new RequestHandlerServiceProvider());
$container->register(new SlimServiceProvider());

$web = new App(
    $container['api-http.response.factory'],
    $container[PsrContainer::class],
    $container[CallableResolver::class],
    $container[RouteCollector::class]
);

$web->addErrorMiddleware($container['debug'], true, true);

$web->get('/', IndexRequestHandler::class)->setName('index');
$web->group('/api', function (RouteCollectorProxy $group): void {
    $group->get('', SwaggerIndexRequestHandler::class)->setName('swagger_index');
    $group->get('/swagger', SwaggerYamlRequestHandler::class)->setName('swagger_yml');
    $group->get('/ping', PingRequestHandler::class)->setName('ping')->add(AcceptAndContentTypeMiddleware::class);
    $group->group('/pets', function (RouteCollectorProxy $group): void {
        $group->get('', ListRequestHandler::class.Pet::class)->setName('pet_list');
        $group->post('', CreateRequestHandler::class.Pet::class)->setName('pet_create');
        $group->get('/{id}', ReadRequestHandler::class.Pet::class)->setName('pet_read');
        $group->put('/{id}', UpdateRequestHandler::class.Pet::class)->setName('pet_update');
        $group->delete('/{id}', DeleteRequestHandler::class.Pet::class)->setName('pet_delete');
    })->add(AcceptAndContentTypeMiddleware::class);
});

return $web;
