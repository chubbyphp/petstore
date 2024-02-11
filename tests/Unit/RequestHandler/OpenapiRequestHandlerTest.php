<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler;

use App\RequestHandler\OpenapiRequestHandler;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \App\RequestHandler\OpenapiRequestHandler
 *
 * @internal
 */
final class OpenapiRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|StreamInterface $responseStream */
        $responseStream = $this->getMockByCalls(StreamInterface::class);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/x-yaml')->willReturnSelf(),
            Call::create('withHeader')
                ->with('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->willReturnSelf(),
            Call::create('withHeader')->with('Pragma', 'no-cache')->willReturnSelf(),
            Call::create('withHeader')->with('Expires', '0')->willReturnSelf(),
            Call::create('withBody')->with($responseStream)->willReturnSelf(),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
        ]);

        /** @var MockObject|StreamFactoryInterface $streamFactory */
        $streamFactory = $this->getMockByCalls(StreamFactoryInterface::class, [
            Call::create('createStreamFromFile')
                ->with(
                    new ArgumentCallback(static function (string $path): void {
                        self::assertMatchesRegularExpression('#src/RequestHandler/../../openapi\.yml$#', $path);
                    }),
                    'r'
                )
                ->willReturn($responseStream),
        ]);

        $requestHandler = new OpenapiRequestHandler($responseFactory, $streamFactory);

        self::assertSame($response, $requestHandler->handle($request));
    }
}
