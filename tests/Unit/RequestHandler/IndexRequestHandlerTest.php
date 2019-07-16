<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler;

use App\RequestHandler\IndexRequestHandler;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\RequestHandler\IndexRequestHandler
 *
 * @internal
 */
class IndexRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Location', 'https://petstore/api')->willReturnSelf(),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(302, '')->willReturn($response),
        ]);

        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class, [
            Call::create('generateUrl')->with($request, 'swagger_index', [], [])->willReturn('https://petstore/api'),
        ]);

        $requestHandler = new IndexRequestHandler($responseFactory, $router);

        self::assertSame($response, $requestHandler->handle($request));
    }
}
