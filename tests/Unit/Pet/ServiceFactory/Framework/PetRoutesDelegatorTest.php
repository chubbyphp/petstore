<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\ServiceFactory\Framework;

use App\Pet\Model\Pet;
use App\Pet\ServiceFactory\Framework\PetRoutesDelegator;
use Chubbyphp\Api\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Api\RequestHandler\CreateRequestHandler;
use Chubbyphp\Api\RequestHandler\DeleteRequestHandler;
use Chubbyphp\Api\RequestHandler\ListRequestHandler;
use Chubbyphp\Api\RequestHandler\ReadRequestHandler;
use Chubbyphp\Api\RequestHandler\UpdateRequestHandler;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Chubbyphp\Oidc\Middleware\OidcAuthenticationMiddleware;
use Laminas\Stratigility\MiddlewarePipe;
use Mezzio\MiddlewareContainer;
use Mezzio\MiddlewareFactory;
use Mezzio\MiddlewareFactoryInterface;
use Mezzio\Router\Route;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \App\Pet\ServiceFactory\Framework\PetRoutesDelegator
 *
 * @internal
 */
final class PetRoutesDelegatorTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MiddlewareInterface $dummyMiddleware */
        $dummyMiddleware = $builder->create(MiddlewareInterface::class, []);

        /** @var ContainerInterface $middlewareContainerContainer */
        $middlewareContainerContainer = $builder->create(ContainerInterface::class, []);

        $middlewareFactory = new MiddlewareFactory(new MiddlewareContainer($middlewareContainerContainer));

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [MiddlewareFactoryInterface::class], $middlewareFactory),
        ]);

        $oidcAuthentication = $middlewareFactory->lazy(OidcAuthenticationMiddleware::class);
        $accept = $middlewareFactory->lazy(AcceptMiddleware::class);
        $contentType = $middlewareFactory->lazy(ContentTypeMiddleware::class);
        $apiExceptionMiddleware = $middlewareFactory->lazy(ApiExceptionMiddleware::class);

        $petList = $middlewareFactory->lazy(Pet::class.ListRequestHandler::class);
        $petCreate = $middlewareFactory->lazy(Pet::class.CreateRequestHandler::class);
        $petRead = $middlewareFactory->lazy(Pet::class.ReadRequestHandler::class);
        $petUpdate = $middlewareFactory->lazy(Pet::class.UpdateRequestHandler::class);
        $petDelete = $middlewareFactory->lazy(Pet::class.DeleteRequestHandler::class);

        $dummy1 = new Route('/dummy1', $dummyMiddleware, ['GET'], 'dummy1');
        $dummy2 = new Route('/dummy2', $dummyMiddleware, ['GET'], 'dummy2');

        $factory = new PetRoutesDelegator();

        $routes = $factory($container, '', static fn () => [$dummy1, $dummy2]);

        self::assertCount(7, $routes);

        self::assertSame($dummy1, $routes[0]);
        self::assertSame($dummy2, $routes[1]);

        $this->assertRoute($routes[2], '/api/pets', ['GET'], 'pet_list', [$accept, $apiExceptionMiddleware, $oidcAuthentication, $petList]);
        $this->assertRoute($routes[3], '/api/pets', ['POST'], 'pet_create', [$accept, $apiExceptionMiddleware, $oidcAuthentication, $contentType, $petCreate]);
        $this->assertRoute($routes[4], '/api/pets/{id}', ['GET'], 'pet_read', [$accept, $apiExceptionMiddleware, $oidcAuthentication, $petRead]);
        $this->assertRoute($routes[5], '/api/pets/{id}', ['PUT'], 'pet_update', [$accept, $apiExceptionMiddleware, $oidcAuthentication, $contentType, $petUpdate]);
        $this->assertRoute($routes[6], '/api/pets/{id}', ['DELETE'], 'pet_delete', [$accept, $apiExceptionMiddleware, $oidcAuthentication, $petDelete]);
    }

    /**
     * @param array<string>              $methods
     * @param array<MiddlewareInterface> $middlewares
     */
    private function assertRoute(Route $route, string $path, array $methods, string $name, array $middlewares): void
    {
        self::assertSame($path, $route->getPath());
        self::assertSame($methods, $route->getAllowedMethods());
        self::assertSame($name, $route->getName());

        $pipeline = $route->getMiddleware();

        self::assertInstanceOf(MiddlewarePipe::class, $pipeline);
        self::assertEquals($middlewares, iterator_to_array($pipeline, false));
    }
}
