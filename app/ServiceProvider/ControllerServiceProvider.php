<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\ApiHttp\Factory\ErrorFactory;
use App\Controller\Crud\CreateController;
use App\Controller\Crud\DeleteController;
use App\Controller\Crud\ListController;
use App\Controller\Crud\ReadController;
use App\Controller\Crud\UpdateController;
use App\Controller\IndexController;
use App\Controller\PingController;
use App\Controller\Swagger\IndexController as SwaggerIndexController;
use App\Controller\Swagger\YamlController as SwaggerYamlController;
use App\Factory\Collection\PetCollectionFactory;
use App\Factory\Model\PetFactory;
use App\Model\Pet;
use App\Repository\Repository;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ControllerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[CreateController::class.Pet::class] = function () use ($container) {
            return new CreateController(
                $container[ErrorFactory::class],
                $container[PetFactory::class],
                $container[Repository::class.Pet::class],
                $container['api-http.request.manager'],
                $container['api-http.response.manager'],
                $container['validator']
            );
        };

        $container[DeleteController::class.Pet::class] = function () use ($container) {
            return new DeleteController(
                $container[Repository::class.Pet::class],
                $container['api-http.response.manager']
            );
        };

        $container[ListController::class.Pet::class] = function () use ($container) {
            return new ListController(
                $container[ErrorFactory::class],
                $container[PetCollectionFactory::class],
                $container[Repository::class.Pet::class],
                $container['api-http.request.manager'],
                $container['api-http.response.manager'],
                $container['validator']
            );
        };

        $container[ReadController::class.Pet::class] = function () use ($container) {
            return new ReadController(
                $container[Repository::class.Pet::class],
                $container['api-http.response.manager']
            );
        };

        $container[UpdateController::class.Pet::class] = function () use ($container) {
            return new UpdateController(
                $container[ErrorFactory::class],
                $container[Repository::class.Pet::class],
                $container['api-http.request.manager'],
                $container['api-http.response.manager'],
                $container['validator']
            );
        };

        $container[SwaggerIndexController::class] = function () use ($container) {
            return new SwaggerIndexController(
                $container['api-http.response.factory'],
                $container['api-http.stream.factory']
            );
        };

        $container[SwaggerYamlController::class] = function () use ($container) {
            return new SwaggerYamlController(
                $container['api-http.response.factory'],
                $container['api-http.stream.factory']
            );
        };

        $container[IndexController::class] = function () use ($container) {
            return new IndexController($container['api-http.response.factory'], $container[FastRouteRouter::class]);
        };

        $container[PingController::class] = function () use ($container) {
            return new PingController(
                $container['api-http.response.factory'],
                $container['serializer']
            );
        };
    }
}
