<?php

declare(strict_types=1);

namespace App\Pet\ServiceFactory\Framework;

use App\Pet\Model\Pet;
use Chubbyphp\Api\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Api\RequestHandler\CreateRequestHandler;
use Chubbyphp\Api\RequestHandler\DeleteRequestHandler;
use Chubbyphp\Api\RequestHandler\ListRequestHandler;
use Chubbyphp\Api\RequestHandler\ReadRequestHandler;
use Chubbyphp\Api\RequestHandler\UpdateRequestHandler;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Chubbyphp\Oidc\Middleware\OidcAuthenticationMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Routing\RouteCollectorProxy;

final class PetRoutesDelegator
{
    public function __invoke(ContainerInterface $container, mixed $_, callable $callback): RouteCollectorInterface
    {
        /** @var RouteCollectorInterface $routeCollector */
        $routeCollector = $callback();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $container->get(CallableResolverInterface::class);

        $routes = new RouteCollectorProxy($responseFactory, $callableResolver, $container, $routeCollector);

        $routes->group('/api', function (RouteCollectorProxyInterface $group): void {
            $group->group('/pets', function (RouteCollectorProxyInterface $group): void {
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
        })->add(OidcAuthenticationMiddleware::class)->add(ApiExceptionMiddleware::class)->add(AcceptMiddleware::class);

        return $routeCollector;
    }
}
