<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ServiceProvider\MiddlewareServiceProvider;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginInterface;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceProvider\MiddlewareServiceProvider
 *
 * @internal
 */
final class MiddlewareServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'api-http.response.factory' => $this->getMockByCalls(ResponseFactoryInterface::class),
            'api-http.response.manager' => $this->getMockByCalls(ResponseManagerInterface::class),
            'cors' => [
                'allow-origin' => ['https?://localhost:3000' => AllowOriginRegex::class],
                'allow-methods' => [],
                'allow-headers' => [],
                'allow-credentials' => false,
                'expose-headers' => [],
                'max-age' => 600,
            ],
            'negotiator.acceptNegotiator' => $this->getMockByCalls(AcceptNegotiatorInterface::class),
            'negotiator.contentTypeNegotiator' => $this->getMockByCalls(ContentTypeNegotiatorInterface::class),
        ]);

        $serviceProvider = new MiddlewareServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(AcceptAndContentTypeMiddleware::class, $container);
        self::assertArrayHasKey(CorsMiddleware::class, $container);

        self::assertInstanceOf(AcceptAndContentTypeMiddleware::class, $container[AcceptAndContentTypeMiddleware::class]);
        self::assertInstanceOf(CorsMiddleware::class, $container[CorsMiddleware::class]);

        /** @var array<int, AllowOriginInterface> $allowOrigins */
        $allowOrigins = $container['allowOrigins'];

        self::assertCount(1, $allowOrigins);

        self::assertInstanceOf(AllowOriginRegex::class, array_shift($allowOrigins));
    }
}
