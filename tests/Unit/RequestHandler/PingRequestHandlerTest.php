<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler;

use App\RequestHandler\PingRequestHandler;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \App\RequestHandler\PingRequestHandler
 *
 * @internal
 */
final class PingRequestHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $builder = new MockObjectBuilder();

        $request = $builder->create(ServerRequestInterface::class, []);

        $responseBody = $builder->create(StreamInterface::class, [
            new WithCallback('write', static function (string $string): int {
                $data = json_decode($string, true);
                self::assertArrayHasKey('datetime', $data);

                return \strlen($string);
            }),
        ]);

        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'application/json']),
            new WithReturnSelf('withHeader', ['Cache-Control', 'no-cache, no-store, must-revalidate']),
            new WithReturnSelf('withHeader', ['Pragma', 'no-cache']),
            new WithReturnSelf('withHeader', ['Expires', '0']),
            new WithReturn('getBody', [], $responseBody),
        ]);

        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [200, ''], $response),
        ]);

        $requestHandler = new PingRequestHandler($responseFactory);

        self::assertSame($response, $requestHandler->handle($request));
    }
}
