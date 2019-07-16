<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ServiceProvider\MiddlewareServiceProvider;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

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
            'api-http.response.manager' => $this->getMockByCalls(ResponseManagerInterface::class),
            'negotiator.acceptNegotiator' => $this->getMockByCalls(AcceptNegotiatorInterface::class),
            'negotiator.contentTypeNegotiator' => $this->getMockByCalls(ContentTypeNegotiatorInterface::class),
        ]);

        $serviceProvider = new MiddlewareServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(AcceptAndContentTypeMiddleware::class, $container);

        self::assertInstanceOf(AcceptAndContentTypeMiddleware::class, $container[AcceptAndContentTypeMiddleware::class]);
    }
}
