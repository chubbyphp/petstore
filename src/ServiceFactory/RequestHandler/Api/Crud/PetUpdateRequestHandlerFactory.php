<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Crud;

use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Container\ContainerInterface;

final class PetUpdateRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): UpdateRequestHandler
    {
        return new UpdateRequestHandler(
            $container->get(PetRepository::class),
            $container->get(RequestManagerInterface::class),
            $container->get(ResponseManagerInterface::class),
            $container->get(ValidatorInterface::class)
        );
    }
}
