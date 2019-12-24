<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Factory\Collection\PetCollectionFactory;
use App\Factory\CollectionFactoryInterface;
use App\Factory\Model\PetFactory;
use App\Factory\ModelFactoryInterface;
use App\Model\Pet;
use App\Repository\PetRepository;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Crud\CreateRequestHandler;
use App\RequestHandler\Crud\DeleteRequestHandler;
use App\RequestHandler\Crud\ListRequestHandler;
use App\RequestHandler\Crud\ReadRequestHandler;
use App\RequestHandler\Crud\UpdateRequestHandler;
use App\RequestHandler\IndexRequestHandler;
use App\RequestHandler\PingRequestHandler;
use App\RequestHandler\Swagger\IndexRequestHandler as SwaggerIndexRequestHandler;
use App\RequestHandler\Swagger\YamlRequestHandler as SwaggerYamlRequestHandler;
use App\ServiceFactory\RequestHandlerServiceFactory;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\SerializerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @covers \App\ServiceFactory\RequestHandlerServiceFactory
 *
 * @internal
 */
final class RequestHandlerServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new RequestHandlerServiceFactory())();

        self::assertCount(9, $factories);
    }

    public function testPetCreateRequestHandler(): void
    {
        /** @var ModelFactoryInterface|MockObject $petFactory */
        $petFactory = $this->getMockByCalls(ModelFactoryInterface::class);

        /** @var RepositoryInterface|MockObject $petRepository */
        $petRepository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(PetFactory::class)->willReturn($petFactory),
            Call::create('get')->with(PetRepository::class)->willReturn($petRepository),
            Call::create('get')->with('api-http.request.manager')->willReturn($requestManager),
            Call::create('get')->with('api-http.response.manager')->willReturn($responseManager),
            Call::create('get')->with('validator')->willReturn($validator),
        ]);

        $factories = (new RequestHandlerServiceFactory())();

        self::assertArrayHasKey(CreateRequestHandler::class.Pet::class, $factories);

        self::assertInstanceOf(
            CreateRequestHandler::class,
            $factories[CreateRequestHandler::class.Pet::class]($container)
        );
    }

    public function testPetDeleteRequestHandler(): void
    {
        /** @var RepositoryInterface|MockObject $petRepository */
        $petRepository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(PetRepository::class)->willReturn($petRepository),
            Call::create('get')->with('api-http.response.manager')->willReturn($responseManager),
        ]);

        $factories = (new RequestHandlerServiceFactory())();

        self::assertArrayHasKey(DeleteRequestHandler::class.Pet::class, $factories);

        self::assertInstanceOf(
            DeleteRequestHandler::class,
            $factories[DeleteRequestHandler::class.Pet::class]($container)
        );
    }

    public function testPetListRequestHandler(): void
    {
        /** @var CollectionFactoryInterface|MockObject $petCollectionFactory */
        $petCollectionFactory = $this->getMockByCalls(CollectionFactoryInterface::class);

        /** @var RepositoryInterface|MockObject $petRepository */
        $petRepository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(PetCollectionFactory::class)->willReturn($petCollectionFactory),
            Call::create('get')->with(PetRepository::class)->willReturn($petRepository),
            Call::create('get')->with('api-http.request.manager')->willReturn($requestManager),
            Call::create('get')->with('api-http.response.manager')->willReturn($responseManager),
            Call::create('get')->with('validator')->willReturn($validator),
        ]);

        $factories = (new RequestHandlerServiceFactory())();

        self::assertArrayHasKey(ListRequestHandler::class.Pet::class, $factories);

        self::assertInstanceOf(
            ListRequestHandler::class,
            $factories[ListRequestHandler::class.Pet::class]($container)
        );
    }

    public function testPetReadRequestHandler(): void
    {
        /** @var RepositoryInterface|MockObject $petRepository */
        $petRepository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(PetRepository::class)->willReturn($petRepository),
            Call::create('get')->with('api-http.response.manager')->willReturn($responseManager),
        ]);

        $factories = (new RequestHandlerServiceFactory())();

        self::assertArrayHasKey(ReadRequestHandler::class.Pet::class, $factories);

        self::assertInstanceOf(
            ReadRequestHandler::class,
            $factories[ReadRequestHandler::class.Pet::class]($container)
        );
    }

    public function testPetUpdateRequestHandler(): void
    {
        /** @var RepositoryInterface|MockObject $petRepository */
        $petRepository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(PetRepository::class)->willReturn($petRepository),
            Call::create('get')->with('api-http.request.manager')->willReturn($requestManager),
            Call::create('get')->with('api-http.response.manager')->willReturn($responseManager),
            Call::create('get')->with('validator')->willReturn($validator),
        ]);

        $factories = (new RequestHandlerServiceFactory())();

        self::assertArrayHasKey(UpdateRequestHandler::class.Pet::class, $factories);

        self::assertInstanceOf(
            UpdateRequestHandler::class,
            $factories[UpdateRequestHandler::class.Pet::class]($container)
        );
    }

    public function testSwaggerIndexRequestHandler(): void
    {
        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var StreamFactoryInterface|MockObject $streamFactory */
        $streamFactory = $this->getMockByCalls(StreamFactoryInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('api-http.response.factory')->willReturn($responseFactory),
            Call::create('get')->with('api-http.stream.factory')->willReturn($streamFactory),
        ]);

        $factories = (new RequestHandlerServiceFactory())();

        self::assertArrayHasKey(SwaggerIndexRequestHandler::class, $factories);

        self::assertInstanceOf(
            SwaggerIndexRequestHandler::class,
            $factories[SwaggerIndexRequestHandler::class]($container)
        );
    }

    public function testSwaggerYamlRequestHandler(): void
    {
        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var StreamFactoryInterface|MockObject $streamFactory */
        $streamFactory = $this->getMockByCalls(StreamFactoryInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('api-http.response.factory')->willReturn($responseFactory),
            Call::create('get')->with('api-http.stream.factory')->willReturn($streamFactory),
        ]);

        $factories = (new RequestHandlerServiceFactory())();

        self::assertArrayHasKey(SwaggerYamlRequestHandler::class, $factories);

        self::assertInstanceOf(
            SwaggerYamlRequestHandler::class,
            $factories[SwaggerYamlRequestHandler::class]($container)
        );
    }

    public function testIndexRequestHandler(): void
    {
        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('api-http.response.factory')->willReturn($responseFactory),
            Call::create('get')->with(RouterInterface::class)->willReturn($router),
        ]);

        $factories = (new RequestHandlerServiceFactory())();

        self::assertArrayHasKey(IndexRequestHandler::class, $factories);

        self::assertInstanceOf(
            IndexRequestHandler::class,
            $factories[IndexRequestHandler::class]($container)
        );
    }

    public function testPingRequestHandler(): void
    {
        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var SerializerInterface|MockObject $serializer */
        $serializer = $this->getMockByCalls(SerializerInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('api-http.response.factory')->willReturn($responseFactory),
            Call::create('get')->with('serializer')->willReturn($serializer),
        ]);

        $factories = (new RequestHandlerServiceFactory())();

        self::assertArrayHasKey(PingRequestHandler::class, $factories);

        self::assertInstanceOf(
            PingRequestHandler::class,
            $factories[PingRequestHandler::class]($container)
        );
    }
}
