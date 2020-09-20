<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Crud;

use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Psr\Container\ContainerInterface;

final class PetReadRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): ReadRequestHandler
    {
        return new ReadRequestHandler(
            $container->get(PetRepository::class),
            $container->get(ResponseManagerInterface::class)
        );
    }
}
