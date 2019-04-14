<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Middleware\AcceptAndContentTypeMiddleware;
use App\ServiceProvider\MiddlewareServiceProvider;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\SlimPsr15\MiddlewareAdapter;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\MiddlewareServiceProvider
 */
final class MiddlewareServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'api-http.response.manager' => $this->getMockByCalls(ResponseManagerInterface::class),
            'negotiator.acceptNegotiator' => $this->getMockByCalls(AcceptNegotiatorInterface::class),
            'negotiator.contentTypeNegotiator' => $this->getMockByCalls(ContentTypeNegotiatorInterface::class),
        ]);

        $serviceProvider = new MiddlewareServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(AcceptAndContentTypeMiddleware::class, $container);

        self::assertMiddlewareInstanceOf(AcceptAndContentTypeMiddleware::class, $container[AcceptAndContentTypeMiddleware::class]);
    }

    /**
     * @param string            $expectedMiddlewareClass
     * @param MiddlewareAdapter $adapter
     */
    private static function assertMiddlewareInstanceOf(string $expectedMiddlewareClass, MiddlewareAdapter $adapter)
    {
        $reflectionProperty = new \ReflectionProperty($adapter, 'middleware');
        $reflectionProperty->setAccessible(true);

        self::assertInstanceOf($expectedMiddlewareClass, $reflectionProperty->getValue($adapter));
    }
}
