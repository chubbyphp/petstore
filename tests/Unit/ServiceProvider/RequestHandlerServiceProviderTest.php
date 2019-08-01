<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ApiHttp\Factory\InvalidParametersFactory;
use App\ApiHttp\Factory\InvalidParametersFactoryInterface;
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
use App\ServiceProvider\RequestHandlerServiceProvider;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\SerializerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * @covers \App\ServiceProvider\RequestHandlerServiceProvider
 *
 * @internal
 */
final class RequestHandlerServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'api-http.request.manager' => $this->getMockByCalls(RequestManagerInterface::class),
            'api-http.response.factory' => $this->getMockByCalls(ResponseFactoryInterface::class),
            'api-http.response.manager' => $this->getMockByCalls(ResponseManagerInterface::class),
            'api-http.stream.factory' => $this->getMockByCalls(StreamFactoryInterface::class),
            'router' => $this->getMockByCalls(RouteParserInterface::class),
            'serializer' => $this->getMockByCalls(SerializerInterface::class),
            'validator' => $this->getMockByCalls(ValidatorInterface::class),
            InvalidParametersFactory::class => $this->getMockByCalls(InvalidParametersFactoryInterface::class),
            PetCollectionFactory::class => $this->getMockByCalls(CollectionFactoryInterface::class),
            PetFactory::class => $this->getMockByCalls(ModelFactoryInterface::class),
            PetRepository::class => $this->getMockByCalls(RepositoryInterface::class),
        ]);

        $serviceProvider = new RequestHandlerServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(ListRequestHandler::class.Pet::class, $container);
        self::assertArrayHasKey(CreateRequestHandler::class.Pet::class, $container);
        self::assertArrayHasKey(ReadRequestHandler::class.Pet::class, $container);
        self::assertArrayHasKey(UpdateRequestHandler::class.Pet::class, $container);
        self::assertArrayHasKey(DeleteRequestHandler::class.Pet::class, $container);

        self::assertArrayHasKey(SwaggerIndexRequestHandler::class, $container);
        self::assertArrayHasKey(SwaggerYamlRequestHandler::class, $container);

        self::assertArrayHasKey(IndexRequestHandler::class, $container);
        self::assertArrayHasKey(PingRequestHandler::class, $container);

        self::assertInstanceOf(ListRequestHandler::class, $container[ListRequestHandler::class.Pet::class]);
        self::assertInstanceOf(CreateRequestHandler::class, $container[CreateRequestHandler::class.Pet::class]);
        self::assertInstanceOf(ReadRequestHandler::class, $container[ReadRequestHandler::class.Pet::class]);
        self::assertInstanceOf(UpdateRequestHandler::class, $container[UpdateRequestHandler::class.Pet::class]);
        self::assertInstanceOf(DeleteRequestHandler::class, $container[DeleteRequestHandler::class.Pet::class]);

        self::assertInstanceOf(SwaggerIndexRequestHandler::class, $container[SwaggerIndexRequestHandler::class]);
        self::assertInstanceOf(SwaggerYamlRequestHandler::class, $container[SwaggerYamlRequestHandler::class]);

        self::assertInstanceOf(IndexRequestHandler::class, $container[IndexRequestHandler::class]);
        self::assertInstanceOf(PingRequestHandler::class, $container[PingRequestHandler::class]);
    }
}
