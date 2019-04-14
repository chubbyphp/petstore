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
use Chubbyphp\Framework\Router\RouteCollection;
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
        $container[RouteCollection::class] = function () use ($container) {
            $psrContainer = new PsrContainer($container);

            $acceptAndContentTypeMiddleware = new LM($psrContainer, AcceptAndContentTypeMiddleware::class);

            return (new RouteCollection())
                ->get('/', 'index', new LRH($psrContainer, IndexController::class))
                ->group('/api')
                    ->get('', 'swagger_index', new LRH($psrContainer, SwaggerIndexController::class))
                    ->get('/swagger.yml', 'swagger_yml', new LRH($psrContainer, SwaggerYamlController::class))
                    ->get('/ping', 'ping', new LRH($psrContainer, PingController::class),
                        [$acceptAndContentTypeMiddleware]
                    )
                    ->group('/pets', [$acceptAndContentTypeMiddleware])
                        ->get('', 'pet_list', new LRH($psrContainer, ListController::class.Pet::class))
                        ->post('', 'pet_create', new LRH($psrContainer, CreateController::class.Pet::class))
                        ->get('/{id}', 'pet_read', new LRH($psrContainer, ReadController::class.Pet::class))
                        ->put('/{id}', 'pet_update', new LRH($psrContainer, UpdateController::class.Pet::class))
                        ->delete('/{id}', 'pet_delete', new LRH($psrContainer, DeleteController::class.Pet::class))
                    ->end()
                ->end()
            ;
        };

        $container[UrlGenerator::class] = function () use ($container) {
            return new UrlGenerator($container[RouteCollection::class]);
        };
    }
}
