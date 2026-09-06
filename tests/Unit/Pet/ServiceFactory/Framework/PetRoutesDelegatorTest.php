<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\CallableResolverFactory;
use App\Pet\Model\Pet;
use App\Pet\ServiceFactory\Framework\PetRoutesDelegator;
use Chubbyphp\Api\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Api\RequestHandler\CreateRequestHandler;
use Chubbyphp\Api\RequestHandler\DeleteRequestHandler;
use Chubbyphp\Api\RequestHandler\ListRequestHandler;
use Chubbyphp\Api\RequestHandler\ReadRequestHandler;
use Chubbyphp\Api\RequestHandler\UpdateRequestHandler;
use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Chubbyphp\Oidc\Middleware\OidcAuthenticationMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Routing\RouteCollector;

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

        $log = [];

        $responseFactory = new ResponseFactory();

        $response = $responseFactory->createResponse();

        $middleware = static function (string $name) use (&$log): \Closure {
            return static function (ServerRequestInterface $request, RequestHandlerInterface $handler) use (&$log, $name): ResponseInterface {
                $log[] = $name;

                return $handler->handle($request);
            };
        };

        $handler = static function (string $name) use (&$log, $response): \Closure {
            return static function () use (&$log, $name, $response): ResponseInterface {
                $log[] = $name;

                return $response;
            };
        };

        /** @var MiddlewareInterface $accept */
        $accept = $builder->create(MiddlewareInterface::class, array_fill(0, 5, new WithCallback('process', $middleware('accept'))));

        /** @var MiddlewareInterface $apiException */
        $apiException = $builder->create(MiddlewareInterface::class, array_fill(0, 5, new WithCallback('process', $middleware('apiException'))));

        /** @var MiddlewareInterface $oidcAuthentication */
        $oidcAuthentication = $builder->create(MiddlewareInterface::class, array_fill(0, 5, new WithCallback('process', $middleware('oidcAuthentication'))));

        /** @var MiddlewareInterface $contentType */
        $contentType = $builder->create(MiddlewareInterface::class, array_fill(0, 2, new WithCallback('process', $middleware('contentType'))));

        /** @var RequestHandlerInterface $petList */
        $petList = $builder->create(RequestHandlerInterface::class, [new WithCallback('handle', $handler('petList'))]);

        /** @var RequestHandlerInterface $petCreate */
        $petCreate = $builder->create(RequestHandlerInterface::class, [new WithCallback('handle', $handler('petCreate'))]);

        /** @var RequestHandlerInterface $petRead */
        $petRead = $builder->create(RequestHandlerInterface::class, [new WithCallback('handle', $handler('petRead'))]);

        /** @var RequestHandlerInterface $petUpdate */
        $petUpdate = $builder->create(RequestHandlerInterface::class, [new WithCallback('handle', $handler('petUpdate'))]);

        /** @var RequestHandlerInterface $petDelete */
        $petDelete = $builder->create(RequestHandlerInterface::class, [new WithCallback('handle', $handler('petDelete'))]);

        $container = (new ContainerFactory())(new Config([
            'dependencies' => [
                'services' => [
                    ResponseFactoryInterface::class => $responseFactory,
                    AcceptMiddleware::class => $accept,
                    ApiExceptionMiddleware::class => $apiException,
                    OidcAuthenticationMiddleware::class => $oidcAuthentication,
                    ContentTypeMiddleware::class => $contentType,
                    Pet::class.ListRequestHandler::class => $petList,
                    Pet::class.CreateRequestHandler::class => $petCreate,
                    Pet::class.ReadRequestHandler::class => $petRead,
                    Pet::class.UpdateRequestHandler::class => $petUpdate,
                    Pet::class.DeleteRequestHandler::class => $petDelete,
                ],
                'factories' => [
                    CallableResolverInterface::class => CallableResolverFactory::class,
                ],
            ],
        ]));

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $container->get(CallableResolverInterface::class);

        $routeCollector = new RouteCollector(
            $responseFactory,
            $callableResolver,
            $container,
            new RequestHandler(true)
        );

        $factory = new PetRoutesDelegator();

        self::assertSame($routeCollector, $factory($container, '', static fn () => $routeCollector));

        /** @var array<RouteInterface> $routes */
        $routes = array_values($routeCollector->getRoutes());

        self::assertCount(5, $routes);

        $this->assertRoute($routes[0], ['GET'], '/api/pets', 'pet_list', Pet::class.ListRequestHandler::class);
        $this->assertRoute($routes[1], ['POST'], '/api/pets', 'pet_create', Pet::class.CreateRequestHandler::class);
        $this->assertRoute($routes[2], ['GET'], '/api/pets/{id}', 'pet_read', Pet::class.ReadRequestHandler::class);
        $this->assertRoute($routes[3], ['PUT'], '/api/pets/{id}', 'pet_update', Pet::class.UpdateRequestHandler::class);
        $this->assertRoute($routes[4], ['DELETE'], '/api/pets/{id}', 'pet_delete', Pet::class.DeleteRequestHandler::class);

        $serverRequestFactory = new ServerRequestFactory();

        foreach ($routes as $route) {
            self::assertSame($response, $route->run($serverRequestFactory->createServerRequest($route->getMethods()[0], 'http://localhost'.$route->getPattern())));
        }

        self::assertSame([
            'accept', 'apiException', 'oidcAuthentication', 'petList',
            'accept', 'apiException', 'oidcAuthentication', 'contentType', 'petCreate',
            'accept', 'apiException', 'oidcAuthentication', 'petRead',
            'accept', 'apiException', 'oidcAuthentication', 'contentType', 'petUpdate',
            'accept', 'apiException', 'oidcAuthentication', 'petDelete',
        ], $log);
    }

    /**
     * @param array<string> $methods
     */
    private function assertRoute(RouteInterface $route, array $methods, string $pattern, string $name, string $callable): void
    {
        self::assertSame($methods, $route->getMethods());
        self::assertSame($pattern, $route->getPattern());
        self::assertSame($name, $route->getName());
        self::assertSame($callable, $route->getCallable());
    }
}
