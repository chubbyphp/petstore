<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Crud;

use App\Parsing\PetParsing;
use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class PetListRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): ListRequestHandler
    {
        return new ListRequestHandler(
            $container->get(PetParsing::class),
            $container->get(PetRepository::class),
            $container->get(EncoderInterface::class),
            $container->get(ResponseFactoryInterface::class),
        );
    }
}
