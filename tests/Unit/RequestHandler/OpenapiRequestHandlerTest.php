<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler;

use App\RequestHandler\OpenapiRequestHandler;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testHandle(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var StreamInterface $responseStream */
        $responseStream = $builder->create(StreamInterface::class, []);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'application/x-yaml']),
            new WithReturnSelf('withHeader', ['Cache-Control', 'no-cache, no-store, must-revalidate']),
            new WithReturnSelf('withHeader', ['Pragma', 'no-cache']),
            new WithReturnSelf('withHeader', ['Expires', '0']),
            new WithReturnSelf('withBody', [$responseStream]),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [200, ''], $response),
        ]);

        /** @var StreamFactoryInterface $streamFactory */
        $streamFactory = $builder->create(StreamFactoryInterface::class, [
            new WithCallback(
                'createStreamFromFile',
                static function (string $filename, string $mode) use ($responseStream): StreamInterface {
                    self::assertMatchesRegularExpression('#src/RequestHandler/../../openapi\.yml$#', $filename);
                    self::assertSame('r', $mode);

                    return $responseStream;
                }
            ),
        ]);

        $requestHandler = new OpenapiRequestHandler($responseFactory, $streamFactory);

        self::assertSame($response, $requestHandler->handle($request));
    }
}
