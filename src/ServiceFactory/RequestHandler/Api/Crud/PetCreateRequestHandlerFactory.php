<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Crud;

use App\Factory\Model\PetFactory;
use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Container\ContainerInterface;

final class PetCreateRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): CreateRequestHandler
    {
        return new CreateRequestHandler(
            $container->get(PetFactory::class),
            $container->get(PetRepository::class),
            $container->get(RequestManagerInterface::class),
            $container->get(ResponseManagerInterface::class),
            $container->get(ValidatorInterface::class)
        );
    }
}
