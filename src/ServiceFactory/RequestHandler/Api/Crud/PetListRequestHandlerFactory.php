<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Crud;

use App\Factory\Collection\PetCollectionFactory;
use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Container\ContainerInterface;

final class PetListRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): ListRequestHandler
    {
        return new ListRequestHandler(
            $container->get(PetCollectionFactory::class),
            $container->get(PetRepository::class),
            $container->get(RequestManagerInterface::class),
            $container->get(ResponseManagerInterface::class),
            $container->get(ValidatorInterface::class)
        );
    }
}
