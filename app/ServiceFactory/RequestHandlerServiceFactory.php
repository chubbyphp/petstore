<?php

declare(strict_types=1);

namespace App\ServiceFactory;

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
use Chubbyphp\Framework\Router\RouterInterface;
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
            SwaggerIndexRequestHandler::class => static function (ContainerInterface $container) {
                return new SwaggerIndexRequestHandler(
                    $container->get('api-http.response.factory'),
                    $container->get('api-http.stream.factory')
                );
            },
            SwaggerYamlRequestHandler::class => static function (ContainerInterface $container) {
                return new SwaggerYamlRequestHandler(
                    $container->get('api-http.response.factory'),
                    $container->get('api-http.stream.factory')
                );
            },
            IndexRequestHandler::class => static function (ContainerInterface $container) {
                return new IndexRequestHandler(
                    $container->get('api-http.response.factory'),
                    $container->get(RouterInterface::class)
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
