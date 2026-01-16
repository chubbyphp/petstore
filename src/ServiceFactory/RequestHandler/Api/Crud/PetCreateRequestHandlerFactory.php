<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Crud;

use App\Parsing\PetParsing;
use App\Repository\PetRepository;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class PetCreateRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): CreateRequestHandler
    {
        /** @var DecoderInterface $decoder */
        $decoder = $container->get(DecoderInterface::class);

        /** @var PetParsing $parsing */
        $parsing = $container->get(PetParsing::class);

        /** @var PetRepository $repository */
        $repository = $container->get(PetRepository::class);

        /** @var EncoderInterface $encoder */
        $encoder = $container->get(EncoderInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        return new CreateRequestHandler(
            $decoder,
            $parsing,
            $repository,
            $encoder,
            $responseFactory,
        );
    }
}
