<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Factory\Collection\PetCollectionFactory;
use App\Factory\Model\PetFactory;
use App\Model\Pet;
use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\Api\PingRequestHandler;
use App\RequestHandler\Api\Swagger\IndexRequestHandler;
use App\RequestHandler\Api\Swagger\YamlRequestHandler;
use Psr\Container\ContainerInterface;

final class RequestHandlerServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            CreateRequestHandler::class.Pet::class => static function (ContainerInterface $container) {
                return new CreateRequestHandler(
                    $container->get(PetFactory::class),
                    $container->get(PetRepository::class),
                    $container->get('api-http.request.manager'),
                    $container->get('api-http.response.manager'),
                    $container->get('validator')
                );
            },
            DeleteRequestHandler::class.Pet::class => static function (ContainerInterface $container) {
                return new DeleteRequestHandler(
                    $container->get(PetRepository::class),
                    $container->get('api-http.response.manager')
                );
            },
            ListRequestHandler::class.Pet::class => static function (ContainerInterface $container) {
                return new ListRequestHandler(
                    $container->get(PetCollectionFactory::class),
                    $container->get(PetRepository::class),
                    $container->get('api-http.request.manager'),
                    $container->get('api-http.response.manager'),
                    $container->get('validator')
                );
            },
            ReadRequestHandler::class.Pet::class => static function (ContainerInterface $container) {
                return new ReadRequestHandler(
                    $container->get(PetRepository::class),
                    $container->get('api-http.response.manager')
                );
            },
            UpdateRequestHandler::class.Pet::class => static function (ContainerInterface $container) {
                return new UpdateRequestHandler(
                    $container->get(PetRepository::class),
                    $container->get('api-http.request.manager'),
                    $container->get('api-http.response.manager'),
                    $container->get('validator')
                );
            },
            IndexRequestHandler::class => static function (ContainerInterface $container) {
                return new IndexRequestHandler(
                    $container->get('api-http.response.factory'),
                    $container->get('api-http.stream.factory')
                );
            },
            YamlRequestHandler::class => static function (ContainerInterface $container) {
                return new YamlRequestHandler(
                    $container->get('api-http.response.factory'),
                    $container->get('api-http.stream.factory')
                );
            },
            PingRequestHandler::class => static function (ContainerInterface $container) {
                return new PingRequestHandler(
                    $container->get('api-http.response.factory'),
                    $container->get('serializer')
                );
            },
        ];
    }
}
