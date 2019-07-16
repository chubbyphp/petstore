<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Model\Pet;
use App\RequestHandler\Crud\CreateRequestHandler;
use App\RequestHandler\Crud\DeleteRequestHandler;
use App\RequestHandler\Crud\ListRequestHandler;
use App\RequestHandler\Crud\ReadRequestHandler;
use App\RequestHandler\Crud\UpdateRequestHandler;
use App\RequestHandler\IndexRequestHandler;
use App\RequestHandler\PingRequestHandler;
use App\RequestHandler\Swagger\IndexRequestHandler as SwaggerIndexRequestHandler;
use App\RequestHandler\Swagger\YamlRequestHandler as SwaggerYamlRequestHandler;
use App\ServiceProvider\ChubbyphpFrameworkProvider;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceProvider\ChubbyphpFrameworkProvider
 *
 * @internal
 */
final class ChubbyphpFrameworkProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'api-http.response.factory' => $this->getMockByCalls(ResponseFactoryInterface::class),
            'debug' => false,
            'routerCacheFile' => null,
        ]);

        $serviceProvider = new ChubbyphpFrameworkProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(ExceptionHandler::class, $container);
        self::assertArrayHasKey(FastRouteRouter::class, $container);
        self::assertArrayHasKey(MiddlewareDispatcher::class, $container);
        self::assertArrayHasKey('routes', $container);

        self::assertInstanceOf(ExceptionHandler::class, $container[ExceptionHandler::class]);
        self::assertInstanceOf(FastRouteRouter::class, $container[FastRouteRouter::class]);
        self::assertInstanceOf(MiddlewareDispatcher::class, $container[MiddlewareDispatcher::class]);

        /** @var RouteInterface[] $routes */
        $routes = $container['routes'];

        self::assertCount(9, $routes);

        self::assertRoute(array_shift($routes), 'index', 'GET', '/', [], [], IndexRequestHandler::class);

        self::assertRoute(array_shift($routes), 'swagger_index', 'GET', '/api', [],
            [],
            SwaggerIndexRequestHandler::class
        );

        self::assertRoute(array_shift($routes), 'swagger_yml', 'GET', '/api/swagger', [],
            [],
            SwaggerYamlRequestHandler::class
        );

        self::assertRoute(array_shift($routes), 'ping', 'GET', '/api/ping', [],
            [AcceptAndContentTypeMiddleware::class],
            PingRequestHandler::class
        );

        self::assertRoute(array_shift($routes), 'pet_list', 'GET', '/api/pets', [],
            [AcceptAndContentTypeMiddleware::class],
            ListRequestHandler::class.Pet::class
        );

        self::assertRoute(array_shift($routes), 'pet_create', 'POST', '/api/pets', [],
            [AcceptAndContentTypeMiddleware::class],
            CreateRequestHandler::class.Pet::class
        );

        self::assertRoute(array_shift($routes), 'pet_read', 'GET', '/api/pets/{id}', [],
            [AcceptAndContentTypeMiddleware::class],
            ReadRequestHandler::class.Pet::class
        );

        self::assertRoute(array_shift($routes), 'pet_update', 'PUT', '/api/pets/{id}', [],
            [AcceptAndContentTypeMiddleware::class],
            UpdateRequestHandler::class.Pet::class
        );

        self::assertRoute(array_shift($routes), 'pet_delete', 'DELETE', '/api/pets/{id}', [],
            [AcceptAndContentTypeMiddleware::class],
            DeleteRequestHandler::class.Pet::class
        );
    }

    /**
     * @param RouteInterface $route
     * @param string         $name
     * @param string         $method
     * @param string         $path
     * @param array          $pathOptions
     * @param string[]       $middlewareIds
     * @param string         $requestHandlerId
     */
    private static function assertRoute(
        RouteInterface $route,
        string $name,
        string $method,
        string $path,
        array $pathOptions,
        array $middlewareIds,
        string $requestHandlerId
    ): void {
        self::assertSame($name, $route->getName());
        self::assertSame($method, $route->getMethod());
        self::assertSame($path, $route->getPath());
        self::assertSame($pathOptions, $route->getPathOptions());
        self::assertMiddlewares($middlewareIds, $route->getMiddlewares());
        self::assertRequestHandler($requestHandlerId, $route->getRequestHandler());
    }

    /**
     * @param string[]         $ids
     * @param LazyMiddleware[] $middlewares
     */
    private static function assertMiddlewares(array $ids, array $middlewares): void
    {
        self::assertCount(count($ids), $middlewares);

        foreach ($ids as $i => $id) {
            self::assertMiddleware($id, $middlewares[$i]);
        }
    }

    /**
     * @param string         $id
     * @param LazyMiddleware $lazyMiddleware
     */
    private static function assertMiddleware(string $id, LazyMiddleware $lazyMiddleware): void
    {
        $reflectionProperty = new \ReflectionProperty($lazyMiddleware, 'id');
        $reflectionProperty->setAccessible(true);

        self::assertSame($id, $reflectionProperty->getValue($lazyMiddleware));
    }

    /**
     * @param string             $id
     * @param LazyRequestHandler $requestHander
     */
    private static function assertRequestHandler(string $id, LazyRequestHandler $requestHander): void
    {
        $reflectionProperty = new \ReflectionProperty($requestHander, 'id');
        $reflectionProperty->setAccessible(true);

        self::assertSame($id, $reflectionProperty->getValue($requestHander));
    }
}
