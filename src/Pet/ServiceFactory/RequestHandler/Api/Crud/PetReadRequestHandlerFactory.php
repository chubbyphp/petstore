<?php

declare(strict_types=1);

namespace App\Pet\ServiceFactory\RequestHandler\Api\Crud;

use App\Core\RequestHandler\Api\Crud\ReadRequestHandler;
use App\Pet\Parsing\PetParsing;
use App\Pet\Repository\PetRepository;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class PetReadRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): ReadRequestHandler
    {
        /** @var PetParsing $parsing */
        $parsing = $container->get(PetParsing::class);

        /** @var PetRepository $repository */
        $repository = $container->get(PetRepository::class);

        /** @var EncoderInterface $encoder */
        $encoder = $container->get(EncoderInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        return new ReadRequestHandler($parsing, $repository, $encoder, $responseFactory);
    }
}
