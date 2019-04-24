<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ApiHttp\Factory\ErrorFactory;
use App\ApiHttp\Factory\ErrorFactoryInterface;
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
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\SerializerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseFactoryInterface;

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
            'serializer' => $this->getMockByCalls(SerializerInterface::class),
            'validator' => $this->getMockByCalls(ValidatorInterface::class),
            ErrorFactory::class => $this->getMockByCalls(ErrorFactoryInterface::class),
            PetCollectionFactory::class => $this->getMockByCalls(CollectionFactoryInterface::class),
            PetFactory::class => $this->getMockByCalls(ModelFactoryInterface::class),
            Repository::class.Pet::class => $this->getMockByCalls(RepositoryInterface::class),
            FastRouteRouter::class => $this->getMockByCalls(RouterInterface::class),
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

        self::assertInstanceOf(ListController::class, $container[ListController::class.Pet::class]);
        self::assertInstanceOf(CreateController::class, $container[CreateController::class.Pet::class]);
        self::assertInstanceOf(ReadController::class, $container[ReadController::class.Pet::class]);
        self::assertInstanceOf(UpdateController::class, $container[UpdateController::class.Pet::class]);
        self::assertInstanceOf(DeleteController::class, $container[DeleteController::class.Pet::class]);

        self::assertInstanceOf(SwaggerIndexController::class, $container[SwaggerIndexController::class]);
        self::assertInstanceOf(SwaggerYamlController::class, $container[SwaggerYamlController::class]);

        self::assertInstanceOf(IndexController::class, $container[IndexController::class]);
        self::assertInstanceOf(PingController::class, $container[PingController::class]);
    }
}
