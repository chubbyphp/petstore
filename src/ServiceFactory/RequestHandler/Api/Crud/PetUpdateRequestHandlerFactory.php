<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Crud;

use App\Parsing\PetParsing;
use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class PetUpdateRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): UpdateRequestHandler
    {
        return new UpdateRequestHandler(
            $container->get(DecoderInterface::class),
            $container->get(PetParsing::class),
            $container->get(PetRepository::class),
            $container->get(EncoderInterface::class),
            $container->get(ResponseFactoryInterface::class),
        );
    }
}
