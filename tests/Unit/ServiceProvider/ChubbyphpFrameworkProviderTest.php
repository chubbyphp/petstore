<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Controller\Crud\CreateController;
use App\Controller\Crud\DeleteController;
use App\Controller\Crud\ListController;
use App\Controller\Crud\ReadController;
use App\Controller\Crud\UpdateController;
use App\Controller\IndexController;
use App\Controller\PingController;
use App\Controller\Swagger\IndexController as SwaggerIndexController;
use App\Controller\Swagger\YamlController as SwaggerYamlController;
use App\Middleware\AcceptAndContentTypeMiddleware;
use App\Model\Pet;
use App\ServiceProvider\ChubbyphpFrameworkProvider;
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

        self::assertRoute(array_shift($routes), 'index', 'GET', '/', [], [], IndexController::class);

        self::assertRoute(array_shift($routes), 'swagger_index', 'GET', '/api', [],
            [],
            SwaggerIndexController::class
        );

        self::assertRoute(array_shift($routes), 'swagger_yml', 'GET', '/api/swagger.yml', [],
            [],
            SwaggerYamlController::class
        );

        self::assertRoute(array_shift($routes), 'ping', 'GET', '/api/ping', [],
            [AcceptAndContentTypeMiddleware::class],
            PingController::class
        );

        self::assertRoute(array_shift($routes), 'pet_list', 'GET', '/api/pets', [],
            [AcceptAndContentTypeMiddleware::class],
            ListController::class.Pet::class
        );

        self::assertRoute(array_shift($routes), 'pet_create', 'POST', '/api/pets', [],
            [AcceptAndContentTypeMiddleware::class],
            CreateController::class.Pet::class
        );

        self::assertRoute(array_shift($routes), 'pet_read', 'GET', '/api/pets/{id}', [],
            [AcceptAndContentTypeMiddleware::class],
            ReadController::class.Pet::class
        );

        self::assertRoute(array_shift($routes), 'pet_update', 'PUT', '/api/pets/{id}', [],
            [AcceptAndContentTypeMiddleware::class],
            UpdateController::class.Pet::class
        );

        self::assertRoute(array_shift($routes), 'pet_delete', 'DELETE', '/api/pets/{id}', [],
            [AcceptAndContentTypeMiddleware::class],
            DeleteController::class.Pet::class
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
    ) {
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
    private static function assertMiddlewares(array $ids, array $middlewares)
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
    private static function assertMiddleware(string $id, LazyMiddleware $lazyMiddleware)
    {
        $reflectionProperty = new \ReflectionProperty($lazyMiddleware, 'id');
        $reflectionProperty->setAccessible(true);

        self::assertSame($id, $reflectionProperty->getValue($lazyMiddleware));
    }

    /**
     * @param string             $id
     * @param LazyRequestHandler $requestHander
     */
    private static function assertRequestHandler(string $id, LazyRequestHandler $requestHander)
    {
        $reflectionProperty = new \ReflectionProperty($requestHander, 'id');
        $reflectionProperty->setAccessible(true);

        self::assertSame($id, $reflectionProperty->getValue($requestHander));
    }
}
