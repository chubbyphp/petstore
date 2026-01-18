<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\ServiceFactory\RequestHandler;

use App\Pet\Repository\PetRepository;
use App\Pet\ServiceFactory\RequestHandler\PetDeleteRequestHandlerFactory;
use Chubbyphp\Api\Repository\RepositoryInterface;
use Chubbyphp\Api\RequestHandler\DeleteRequestHandler;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\Pet\ServiceFactory\RequestHandler\PetDeleteRequestHandlerFactory
 *
 * @internal
 */
final class PetDeleteRequestHandlerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var RepositoryInterface $petRepository */
        $petRepository = $builder->create(RepositoryInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [PetRepository::class], $petRepository),
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
        ]);

        $factory = new PetDeleteRequestHandlerFactory();

        self::assertInstanceOf(DeleteRequestHandler::class, $factory($container));
    }
}
