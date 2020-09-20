<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\RequestHandler\Api;

use App\Factory\Collection\PetCollectionFactory;
use App\Factory\CollectionFactoryInterface;
use App\Repository\PetRepository;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\ServiceFactory\RequestHandler\Api\Crud\PetListRequestHandlerFactory;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\RequestHandler\Api\Crud\PetListRequestHandlerFactory
 *
 * @internal
 */
final class PetListRequestHandlerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var CollectionFactoryInterface $factory */
        $collectionfactory = $this->getMockByCalls(CollectionFactoryInterface::class);

        /** @var RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var RequestManagerInterface $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class);

        /** @var ResponseManagerInterface $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var ValidatorInterface $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(PetCollectionFactory::class)->willReturn($collectionfactory),
            Call::create('get')->with(PetRepository::class)->willReturn($repository),
            Call::create('get')->with(RequestManagerInterface::class)->willReturn($requestManager),
            Call::create('get')->with(ResponseManagerInterface::class)->willReturn($responseManager),
            Call::create('get')->with(ValidatorInterface::class)->willReturn($validator),
        ]);

        $factory = new PetListRequestHandlerFactory();

        self::assertInstanceOf(ListRequestHandler::class, $factory($container));
    }
}
