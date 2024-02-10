<?php

declare(strict_types=1);

namespace App;

use App\Middleware\ApiExceptionMiddleware;
use App\Middleware\ConvertHttpExceptionMiddleware;
use App\Model\Pet;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\OpenapiRequestHandler;
use App\RequestHandler\PingRequestHandler;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Routing\RouteCollectorProxy;

require __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    /** @var ContainerInterface $container */
    $container = (require __DIR__.'/container.php')($env);

    $web = new App(
        $container->get(ResponseFactoryInterface::class),
        $container,
        $container->get(CallableResolverInterface::class),
        $container->get(RouteCollectorInterface::class)
    );

    $web->add(CorsMiddleware::class);
    $web->add(ConvertHttpExceptionMiddleware::class);
    $web->addErrorMiddleware($container->get('config')['debug'], true, true);

    $web->get('/openapi', OpenapiRequestHandler::class)->setName('openapi');
    $web->get('/ping', PingRequestHandler::class)->setName('ping');
    $web->group('/api', function (RouteCollectorProxy $group): void {
        $group->group('/pets', function (RouteCollectorProxy $group): void {
            $group->get('', Pet::class.ListRequestHandler::class)->setName('pet_list');
            $group->post('', Pet::class.CreateRequestHandler::class)->setName('pet_create')
                ->add(ContentTypeMiddleware::class)
            ;
            $group->get('/{id}', Pet::class.ReadRequestHandler::class)->setName('pet_read');
            $group->put('/{id}', Pet::class.UpdateRequestHandler::class)->setName('pet_update')
                ->add(ContentTypeMiddleware::class)
            ;
            $group->delete('/{id}', Pet::class.DeleteRequestHandler::class)->setName('pet_delete');
        });
    })->add(ApiExceptionMiddleware::class)->add(AcceptMiddleware::class);

    return $web;
};
