<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\RequestHandler\Api\Crud;

use App\Repository\PetRepository;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\ServiceFactory\RequestHandler\Api\Crud\PetDeleteRequestHandlerFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceFactory\RequestHandler\Api\Crud\PetDeleteRequestHandlerFactory
 *
 * @internal
 */
final class PetDeleteRequestHandlerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var RepositoryInterface $petRepository */
        $petRepository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(PetRepository::class)->willReturn($petRepository),
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
        ]);

        $factory = new PetDeleteRequestHandlerFactory();

        self::assertInstanceOf(DeleteRequestHandler::class, $factory($container));
    }
}
