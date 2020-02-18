<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Api;

use App\RequestHandler\Api\PingRequestHandler;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \App\RequestHandler\Api\PingRequestHandler
 *
 * @internal
 */
final class PingRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var StreamInterface|MockObject $body */
        $body = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with('{"date": "now"}'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/json')->willReturnSelf(),
            Call::create('withHeader')
                ->with('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->willReturnSelf(),
            Call::create('withHeader')->with('Pragma', 'no-cache')->willReturnSelf(),
            Call::create('withHeader')->with('Expires', '0')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($body),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
        ]);

        /** @var SerializerInterface|MockObject $serializer */
        $serializer = $this->getMockByCalls(SerializerInterface::class, [
            Call::create('encode')
                ->with(
                    new ArgumentCallback(function ($data): void {
                        self::assertIsArray($data);

                        self::assertArrayHasKey('date', $data);
                    }),
                    'application/json'
                )
                ->willReturn('{"date": "now"}'),
        ]);

        $requestHandler = new PingRequestHandler($responseFactory, $serializer);

        self::assertSame($response, $requestHandler->handle($request));
    }
}
