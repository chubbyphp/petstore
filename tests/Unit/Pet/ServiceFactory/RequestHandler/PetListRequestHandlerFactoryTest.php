<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\ServiceFactory\RequestHandler;

use App\Pet\Parsing\PetParsing;
use App\Pet\Repository\PetRepository;
use App\Pet\ServiceFactory\RequestHandler\PetListRequestHandlerFactory;
use Chubbyphp\Api\Parsing\ParsingInterface;
use Chubbyphp\Api\Repository\RepositoryInterface;
use Chubbyphp\Api\RequestHandler\ListRequestHandler;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\Pet\ServiceFactory\RequestHandler\PetListRequestHandlerFactory
 *
 * @internal
 */
final class PetListRequestHandlerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ParsingInterface $petParsing */
        $petParsing = $builder->create(ParsingInterface::class, []);

        /** @var RepositoryInterface $petRepository */
        $petRepository = $builder->create(RepositoryInterface::class, []);

        /** @var EncoderInterface $encoder */
        $encoder = $builder->create(EncoderInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [PetParsing::class], $petParsing),
            new WithReturn('get', [PetRepository::class], $petRepository),
            new WithReturn('get', [EncoderInterface::class], $encoder),
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
        ]);

        $factory = new PetListRequestHandlerFactory();

        self::assertInstanceOf(ListRequestHandler::class, $factory($container));
    }
}
