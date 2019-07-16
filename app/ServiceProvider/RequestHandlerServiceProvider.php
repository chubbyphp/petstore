<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\ApiHttp\Factory\InvalidParametersFactory;
use App\Factory\Collection\PetCollectionFactory;
use App\Factory\Model\PetFactory;
use App\Model\Pet;
use App\Repository\PetRepository;
use App\RequestHandler\Crud\CreateRequestHandler;
use App\RequestHandler\Crud\DeleteRequestHandler;
use App\RequestHandler\Crud\ListRequestHandler;
use App\RequestHandler\Crud\ReadRequestHandler;
use App\RequestHandler\Crud\UpdateRequestHandler;
use App\RequestHandler\IndexRequestHandler;
use App\RequestHandler\PingRequestHandler;
use App\RequestHandler\Swagger\IndexRequestHandler as SwaggerIndexRequestHandler;
use App\RequestHandler\Swagger\YamlRequestHandler as SwaggerYamlRequestHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class RequestHandlerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[CreateRequestHandler::class.Pet::class] = function () use ($container) {
            return new CreateRequestHandler(
                $container[InvalidParametersFactory::class],
                $container[PetFactory::class],
                $container[PetRepository::class],
                $container['api-http.request.manager'],
                $container['api-http.response.manager'],
                $container['validator']
            );
        };

        $container[DeleteRequestHandler::class.Pet::class] = function () use ($container) {
            return new DeleteRequestHandler(
                $container[PetRepository::class],
                $container['api-http.response.manager']
            );
        };

        $container[ListRequestHandler::class.Pet::class] = function () use ($container) {
            return new ListRequestHandler(
                $container[InvalidParametersFactory::class],
                $container[PetCollectionFactory::class],
                $container[PetRepository::class],
                $container['api-http.request.manager'],
                $container['api-http.response.manager'],
                $container['validator']
            );
        };

        $container[ReadRequestHandler::class.Pet::class] = function () use ($container) {
            return new ReadRequestHandler(
                $container[PetRepository::class],
                $container['api-http.response.manager']
            );
        };

        $container[UpdateRequestHandler::class.Pet::class] = function () use ($container) {
            return new UpdateRequestHandler(
                $container[InvalidParametersFactory::class],
                $container[PetRepository::class],
                $container['api-http.request.manager'],
                $container['api-http.response.manager'],
                $container['validator']
            );
        };

        $container[SwaggerIndexRequestHandler::class] = function () use ($container) {
            return new SwaggerIndexRequestHandler(
                $container['api-http.response.factory'],
                $container['api-http.stream.factory']
            );
        };

        $container[SwaggerYamlRequestHandler::class] = function () use ($container) {
            return new SwaggerYamlRequestHandler(
                $container['api-http.response.factory'],
                $container['api-http.stream.factory']
            );
        };

        $container[IndexRequestHandler::class] = function () use ($container) {
            return new IndexRequestHandler(
                $container['api-http.response.factory'],
                $container['router']
            );
        };

        $container[PingRequestHandler::class] = function () use ($container) {
            return new PingRequestHandler(
                $container['api-http.response.factory'],
                $container['serializer']
            );
        };
    }
}
