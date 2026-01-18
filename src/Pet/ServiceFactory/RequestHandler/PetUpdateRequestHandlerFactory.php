<?php

declare(strict_types=1);

namespace App\Pet\ServiceFactory\RequestHandler;

use App\Pet\Parsing\PetParsing;
use App\Pet\Repository\PetRepository;
use Chubbyphp\Api\RequestHandler\UpdateRequestHandler;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class PetUpdateRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): UpdateRequestHandler
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

        return new UpdateRequestHandler(
            $decoder,
            $parsing,
            $repository,
            $encoder,
            $responseFactory,
        );
    }
}
