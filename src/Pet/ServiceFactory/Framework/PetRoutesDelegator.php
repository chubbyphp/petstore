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
use Mezzio\MiddlewareFactoryInterface;
use Mezzio\Router\Route;
use Psr\Container\ContainerInterface;

final class PetRoutesDelegator
{
    /**
     * @return array<Route>
     */
    public function __invoke(ContainerInterface $container, mixed $_, callable $callback): array
    {
        /** @var array<Route> $routes */
        $routes = $callback();

        /** @var MiddlewareFactoryInterface $middlewareFactory */
        $middlewareFactory = $container->get(MiddlewareFactoryInterface::class);

        $oidcAuthentication = $middlewareFactory->lazy(OidcAuthenticationMiddleware::class);
        $accept = $middlewareFactory->lazy(AcceptMiddleware::class);
        $contentType = $middlewareFactory->lazy(ContentTypeMiddleware::class);
        $apiExceptionMiddleware = $middlewareFactory->lazy(ApiExceptionMiddleware::class);

        $petList = $middlewareFactory->lazy(Pet::class.ListRequestHandler::class);
        $petCreate = $middlewareFactory->lazy(Pet::class.CreateRequestHandler::class);
        $petRead = $middlewareFactory->lazy(Pet::class.ReadRequestHandler::class);
        $petUpdate = $middlewareFactory->lazy(Pet::class.UpdateRequestHandler::class);
        $petDelete = $middlewareFactory->lazy(Pet::class.DeleteRequestHandler::class);

        $apiMiddlewares = [$accept, $apiExceptionMiddleware, $oidcAuthentication];

        return [
            ...$routes,
            new Route('/api/pets', $middlewareFactory->pipeline([...$apiMiddlewares, $petList]), ['GET'], 'pet_list'),
            new Route('/api/pets', $middlewareFactory->pipeline([...$apiMiddlewares, $contentType, $petCreate]), ['POST'], 'pet_create'),
            new Route('/api/pets/{id}', $middlewareFactory->pipeline([...$apiMiddlewares, $petRead]), ['GET'], 'pet_read'),
            new Route('/api/pets/{id}', $middlewareFactory->pipeline([...$apiMiddlewares, $contentType, $petUpdate]), ['PUT'], 'pet_update'),
            new Route('/api/pets/{id}', $middlewareFactory->pipeline([...$apiMiddlewares, $petDelete]), ['DELETE'], 'pet_delete'),
        ];
    }
}
