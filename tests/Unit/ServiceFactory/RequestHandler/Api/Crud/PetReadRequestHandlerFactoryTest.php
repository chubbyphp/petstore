<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\RequestHandler\Api;

use App\Repository\PetRepository;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\ServiceFactory\RequestHandler\Api\Crud\PetReadRequestHandlerFactory;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\RequestHandler\Api\Crud\PetReadRequestHandlerFactory
 *
 * @internal
 */
final class PetReadRequestHandlerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var ResponseManagerInterface $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(PetRepository::class)->willReturn($repository),
            Call::create('get')->with(ResponseManagerInterface::class)->willReturn($responseManager),
        ]);

        $factory = new PetReadRequestHandlerFactory();

        self::assertInstanceOf(ReadRequestHandler::class, $factory($container));
    }
}
