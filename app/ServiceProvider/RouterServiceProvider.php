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
use Chubbyphp\Framework\Middleware\LazyMiddleware as LM;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler as LRH;
use Chubbyphp\Framework\Router\FastRoute\UrlGenerator;
use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Pimple\ServiceProviderInterface;

final class RouterServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['routes'] = function () use ($container) {
            $psrContainer = new PsrContainer($container);

            $acceptAndContentTypeMiddleware = new LM($psrContainer, AcceptAndContentTypeMiddleware::class);

            $indexController = new LRH($psrContainer, IndexController::class);
            $swaggerIndexController = new LRH($psrContainer, SwaggerIndexController::class);
            $swaggerYamlController = new LRH($psrContainer, SwaggerYamlController::class);
            $pingController = new LRH($psrContainer, PingController::class);
            $petListController = new LRH($psrContainer, ListController::class.Pet::class);
            $petCreateController = new LRH($psrContainer, CreateController::class.Pet::class);
            $petReadController = new LRH($psrContainer, ReadController::class.Pet::class);
            $petUpdateController = new LRH($psrContainer, UpdateController::class.Pet::class);
            $petDeleteController = new LRH($psrContainer, DeleteController::class.Pet::class);

            return Group::create('')
                ->route(Route::get('', 'index', $indexController))
                ->group(Group::create('/api')
                    ->route(Route::get('', 'swagger_index', $swaggerIndexController))
                    ->route(Route::get('/swagger.yml', 'swagger_yml', $swaggerYamlController))
                    ->route(Route::get('/ping', 'ping', $pingController)
                        ->middleware($acceptAndContentTypeMiddleware)
                    )
                    ->group(Group::create('/pets')
                        ->route(Route::get('', 'pet_list', $petListController))
                        ->route(Route::post('', 'pet_create', $petCreateController))
                        ->route(Route::get('/{id}', 'pet_read', $petReadController))
                        ->route(Route::put('/{id}', 'pet_update', $petUpdateController))
                        ->route(Route::delete('/{id}', 'pet_delete', $petDeleteController))
                        ->middleware($acceptAndContentTypeMiddleware)
                    )
                )->getRoutes();
        };

        $container[UrlGenerator::class] = function () use ($container) {
            return new UrlGenerator($container['routes']);
        };
    }
}
