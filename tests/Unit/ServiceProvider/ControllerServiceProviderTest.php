<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ApiHttp\Factory\InvalidParametersFactory;
use App\ApiHttp\Factory\InvalidParametersFactoryInterface;
use App\Controller\Crud\CreateController;
use App\Controller\Crud\DeleteController;
use App\Controller\Crud\ListController;
use App\Controller\Crud\ReadController;
use App\Controller\Crud\UpdateController;
use App\Controller\IndexController;
use App\Controller\PingController;
use App\Controller\Swagger\IndexController as SwaggerIndexController;
use App\Controller\Swagger\YamlController as SwaggerYamlController;
use App\Factory\Collection\PetCollectionFactory;
use App\Factory\CollectionFactoryInterface;
use App\Factory\Model\PetFactory;
use App\Factory\ModelFactoryInterface;
use App\Model\Pet;
use App\Repository\Repository;
use App\Repository\RepositoryInterface;
use App\ServiceProvider\ControllerServiceProvider;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\SerializerInterface;
use Chubbyphp\SlimPsr15\RequestHandlerAdapter;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Router;

/**
 * @covers \App\ServiceProvider\ControllerServiceProvider
 */
final class ControllerServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'api-http.request.manager' => $this->getMockByCalls(RequestManagerInterface::class),
            'api-http.response.factory' => $this->getMockByCalls(ResponseFactoryInterface::class),
            'api-http.response.manager' => $this->getMockByCalls(ResponseManagerInterface::class),
            'router' => $this->getMockByCalls(Router::class),
            'serializer' => $this->getMockByCalls(SerializerInterface::class),
            'validator' => $this->getMockByCalls(ValidatorInterface::class),
            InvalidParametersFactory::class => $this->getMockByCalls(InvalidParametersFactoryInterface::class),
            PetCollectionFactory::class => $this->getMockByCalls(CollectionFactoryInterface::class),
            PetFactory::class => $this->getMockByCalls(ModelFactoryInterface::class),
            Repository::class.Pet::class => $this->getMockByCalls(RepositoryInterface::class),
        ]);

        $serviceProvider = new ControllerServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(ListController::class.Pet::class, $container);
        self::assertArrayHasKey(CreateController::class.Pet::class, $container);
        self::assertArrayHasKey(ReadController::class.Pet::class, $container);
        self::assertArrayHasKey(UpdateController::class.Pet::class, $container);
        self::assertArrayHasKey(DeleteController::class.Pet::class, $container);

        self::assertArrayHasKey(SwaggerIndexController::class, $container);
        self::assertArrayHasKey(SwaggerYamlController::class, $container);

        self::assertArrayHasKey(IndexController::class, $container);
        self::assertArrayHasKey(PingController::class, $container);

        self::assertRequestHandlerInstanceOf(ListController::class, $container[ListController::class.Pet::class]);
        self::assertRequestHandlerInstanceOf(CreateController::class, $container[CreateController::class.Pet::class]);
        self::assertRequestHandlerInstanceOf(ReadController::class, $container[ReadController::class.Pet::class]);
        self::assertRequestHandlerInstanceOf(UpdateController::class, $container[UpdateController::class.Pet::class]);
        self::assertRequestHandlerInstanceOf(DeleteController::class, $container[DeleteController::class.Pet::class]);

        self::assertRequestHandlerInstanceOf(SwaggerIndexController::class, $container[SwaggerIndexController::class]);
        self::assertRequestHandlerInstanceOf(SwaggerYamlController::class, $container[SwaggerYamlController::class]);

        self::assertRequestHandlerInstanceOf(IndexController::class, $container[IndexController::class]);
        self::assertRequestHandlerInstanceOf(PingController::class, $container[PingController::class]);
    }

    /**
     * @param string                $expectedRequestHandlerClass
     * @param RequestHandlerAdapter $adapter
     */
    private static function assertRequestHandlerInstanceOf(
        string $expectedRequestHandlerClass,
        RequestHandlerAdapter $adapter
    ) {
        $reflectionProperty = new \ReflectionProperty($adapter, 'requestHandler');
        $reflectionProperty->setAccessible(true);

        self::assertInstanceOf($expectedRequestHandlerClass, $reflectionProperty->getValue($adapter));
    }
}
