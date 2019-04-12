<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Controller\Crud\CreateController;
use App\Controller\Crud\DeleteController;
use App\Controller\Crud\ListController;
use App\Controller\Crud\ReadController;
use App\Controller\Crud\UpdateController;
use App\Controller\IndexController;
use App\Controller\PingController;
use App\Controller\Swagger\IndexController as SwaggerIndexController;
use App\Controller\Swagger\YamlController as SwaggerYamlController;
use App\Middleware\AcceptAndContentTypeMiddleware;
use App\Model\Pet;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\RouteCollection;
use Chubbyphp\Framework\Router\RouteInterface;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Pimple\ServiceProviderInterface;

final class RouteCollectionServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[RouteCollection::class] = function () use ($container) {
            $psrContainer = new PsrContainer($container);

            $acceptAndContentTypeMiddleware = new LazyMiddleware($psrContainer, AcceptAndContentTypeMiddleware::class);

            return (new RouteCollection())
                ->route('/', RouteInterface::GET, 'index',
                    new LazyRequestHandler($psrContainer, IndexController::class)
                )
                ->group('/api')
                    ->route('', RouteInterface::GET, 'swagger_index',
                        new LazyRequestHandler($psrContainer, SwaggerIndexController::class)
                    )
                    ->route('/swagger.yml', RouteInterface::GET, 'swagger_yml',
                        new LazyRequestHandler($psrContainer, SwaggerYamlController::class)
                    )
                    ->route('/ping', RouteInterface::GET, 'ping',
                        new LazyRequestHandler($psrContainer, PingController::class),
                        [$acceptAndContentTypeMiddleware]
                    )
                    ->group('/pets', [$acceptAndContentTypeMiddleware])
                        ->route('', RouteInterface::GET, 'pet_list',
                            new LazyRequestHandler($psrContainer, ListController::class.Pet::class)
                        )
                        ->route('', RouteInterface::POST, 'pet_create',
                            new LazyRequestHandler($psrContainer, CreateController::class.Pet::class)
                        )
                        ->route('/{id}', RouteInterface::GET, 'pet_read',
                            new LazyRequestHandler($psrContainer, ReadController::class.Pet::class)
                        )
                        ->route('/{id}', RouteInterface::PUT, 'pet_update',
                            new LazyRequestHandler($psrContainer, UpdateController::class.Pet::class)
                        )
                        ->route('/{id}', RouteInterface::DELETE, 'pet_delete',
                            new LazyRequestHandler($psrContainer, DeleteController::class.Pet::class)
                        )
                    ->end()
                ->end()
            ;
        };
    }
}
