<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Crud;

use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Psr\Container\ContainerInterface;

final class PetDeleteRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): DeleteRequestHandler
    {
        return new DeleteRequestHandler(
            $container->get(PetRepository::class),
            $container->get(ResponseManagerInterface::class)
        );
    }
}
