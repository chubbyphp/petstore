<?php

declare(strict_types=1);

namespace App\Tests\Unit\Middleware;

use App\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\NegotiatedValueInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\Middleware\AcceptAndContentTypeMiddleware
 */
class AcceptAndContentTypeMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithoutAccept(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Accept')->willReturn('application/xml'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, []);

        $next = function (ServerRequestInterface $request, ResponseInterface $response) {
            self::fail('should not be called');
        };

        /** @var AcceptNegotiatorInterface|MockObject $acceptNegotiator */
        $acceptNegotiator = $this->getMockByCalls(AcceptNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(null),
        ]);

        /** @var ContentTypeNegotiatorInterface|MockObject $contentTypeNegotiator */
        $contentTypeNegotiator = $this->getMockByCalls(ContentTypeNegotiatorInterface::class, []);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createAcceptNotSupported')->with('application/xml')->willReturn($response),
        ]);

        $middleware = new AcceptAndContentTypeMiddleware($acceptNegotiator, $contentTypeNegotiator, $responseManager);

        self::assertSame($response, $middleware($request, $response, $next));
    }

    public function testWithAccept(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('accept', 'application/json')->willReturnSelf(),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, []);

        $next = function (ServerRequestInterface $request, ResponseInterface $response) {
            return $response;
        };

        /** @var NegotiatedValueInterface|MockObject $accept */
        $accept = $this->getMockByCalls(NegotiatedValueInterface::class, [
            Call::create('getValue')->with()->willReturn('application/json'),
        ]);

        /** @var AcceptNegotiatorInterface|MockObject $acceptNegotiator */
        $acceptNegotiator = $this->getMockByCalls(AcceptNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn($accept),
        ]);

        /** @var ContentTypeNegotiatorInterface|MockObject $contentTypeNegotiator */
        $contentTypeNegotiator = $this->getMockByCalls(ContentTypeNegotiatorInterface::class, []);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, []);

        $middleware = new AcceptAndContentTypeMiddleware($acceptNegotiator, $contentTypeNegotiator, $responseManager);

        self::assertSame($response, $middleware($request, $response, $next));
    }

    public function testWithoutContentType(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('accept', 'application/json')->willReturnSelf(),
            Call::create('getMethod')->with()->willReturn('POST'),
            Call::create('getHeaderLine')->with('Content-Type')->willReturn('application/xml'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, []);

        $next = function (ServerRequestInterface $request, ResponseInterface $response) {
            self::fail('should not be called');
        };

        /** @var NegotiatedValueInterface|MockObject $accept */
        $accept = $this->getMockByCalls(NegotiatedValueInterface::class, [
            Call::create('getValue')->with()->willReturn('application/json'),
            Call::create('getValue')->with()->willReturn('application/json'),
        ]);

        /** @var AcceptNegotiatorInterface $acceptNegotiator */
        $acceptNegotiator = $this->getMockByCalls(AcceptNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn($accept),
        ]);

        /** @var ContentTypeNegotiatorInterface|MockObject $contentTypeNegotiator */
        $contentTypeNegotiator = $this->getMockByCalls(ContentTypeNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(null),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createContentTypeNotSupported')
                ->with('application/xml', 'application/json', null)
                ->willReturn($response),
        ]);

        $middleware = new AcceptAndContentTypeMiddleware($acceptNegotiator, $contentTypeNegotiator, $responseManager);

        self::assertSame($response, $middleware($request, $response, $next));
    }

    public function testWithContentType(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('accept', 'application/json')->willReturnSelf(),
            Call::create('getMethod')->with()->willReturn('POST'),
            Call::create('withAttribute')->with('contentType', 'application/json')->willReturnSelf(),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, []);

        $next = function (ServerRequestInterface $request, ResponseInterface $response) {
            return $response;
        };

        /** @var NegotiatedValueInterface|MockObject $accept */
        $accept = $this->getMockByCalls(NegotiatedValueInterface::class, [
            Call::create('getValue')->with()->willReturn('application/json'),
        ]);

        /** @var AcceptNegotiatorInterface|MockObject $acceptNegotiator */
        $acceptNegotiator = $this->getMockByCalls(AcceptNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn($accept),
        ]);

        /** @var NegotiatedValueInterface|MockObject $contentType */
        $contentType = $this->getMockByCalls(NegotiatedValueInterface::class, [
            Call::create('getValue')->with()->willReturn('application/json'),
        ]);

        /** @var ContentTypeNegotiatorInterface|MockObject $contentTypeNegotiator */
        $contentTypeNegotiator = $this->getMockByCalls(ContentTypeNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn($contentType),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, []);

        $middleware = new AcceptAndContentTypeMiddleware($acceptNegotiator, $contentTypeNegotiator, $responseManager);

        self::assertSame($response, $middleware($request, $response, $next));
    }
}
