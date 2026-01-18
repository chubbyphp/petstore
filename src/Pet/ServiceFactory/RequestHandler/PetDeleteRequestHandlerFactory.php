<?php

declare(strict_types=1);

namespace App\Pet\ServiceFactory\RequestHandler;

use App\Pet\Repository\PetRepository;
use Chubbyphp\Api\RequestHandler\DeleteRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class PetDeleteRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): DeleteRequestHandler
    {
        /** @var PetRepository $repository */
        $repository = $container->get(PetRepository::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        return new DeleteRequestHandler($repository, $responseFactory);
    }
}
