<?php

declare(strict_types=1);

namespace App\Tests\Unit\Middleware;

use App\Middleware\ApiExceptionMiddleware;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithException;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\Middleware\ApiExceptionMiddleware
 *
 * @internal
 */
final class ApiExceptionMiddlewareTest extends TestCase
{
    public function testWithDebugAndLoggerWithoutException(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $builder->create(EncoderInterface::class, []);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $builder->create(LoggerInterface::class, []);

        $apiExceptionMiddleware = new ApiExceptionMiddleware($encoder, $responseFactory, true, $logger);

        self::assertSame($response, $apiExceptionMiddleware->process($request, $handler));
    }

    public function testWithDebugAndLoggerWithExceptionAndWithoutAccept(): void
    {
        $builder = new MockObjectBuilder();
        $previousException = new \RuntimeException('previous', 3);
        $exception = new \LogicException('current', 5, $previousException);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['accept', null], null),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithException('handle', [$request], $exception),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $builder->create(EncoderInterface::class, []);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithCallback(
                'error',
                static function (string $message, array $context): void {
                    self::assertSame('Http Exception', $message);
                    self::assertArrayHasKey('backtrace', $context);

                    self::assertCount(2, $context['backtrace']);

                    $trace1 = array_shift($context['backtrace']);

                    self::assertSame(\LogicException::class, $trace1['class']);
                    self::assertSame('current', $trace1['message']);
                    self::assertSame(5, $trace1['code']);
                    self::assertMatchesRegularExpression('/ApiExceptionMiddlewareTest\.php/', $trace1['file']);
                    self::assertIsInt($trace1['line']);
                    self::assertMatchesRegularExpression('/ApiExceptionMiddlewareTest/', $trace1['trace']);

                    $trace2 = array_shift($context['backtrace']);

                    self::assertSame(\RuntimeException::class, $trace2['class']);
                    self::assertSame('previous', $trace2['message']);
                    self::assertSame(3, $trace2['code']);
                    self::assertMatchesRegularExpression('/ApiExceptionMiddlewareTest\.php/', $trace2['file']);
                    self::assertIsInt($trace2['line']);
                    self::assertMatchesRegularExpression('/ApiExceptionMiddlewareTest/', $trace2['trace']);
                }
            ),
        ]);

        $apiExceptionMiddleware = new ApiExceptionMiddleware($encoder, $responseFactory, true, $logger);

        try {
            $apiExceptionMiddleware->process($request, $handler);

            throw new \Exception('Expect exception');
        } catch (\Throwable $e) {
            self::assertSame($exception->getMessage(), $e->getMessage());
        }
    }

    public function testWithDebugAndLoggerWithExceptionAndWithAccept(): void
    {
        $builder = new MockObjectBuilder();
        $previousException = new \RuntimeException('previous', 3);
        $exception = new \LogicException('current', 5, $previousException);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
        ]);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $builder->create(StreamInterface::class, [
            new WithReturn('write', ['encoded'], \strlen('encoded')),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf(
                'withHeader',
                ['Content-Type', 'application/problem+json']
            ),
            new WithReturn('getBody', [], $responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithException('handle', [$request], $exception),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $builder->create(EncoderInterface::class, [
            new WithCallback(
                'encode',
                static function (array $data, string $contentType): string {
                    self::assertSame('https://datatracker.ietf.org/doc/html/rfc2616#section-10.5.1', $data['type']);
                    self::assertSame(500, $data['status']);
                    self::assertSame('Internal Server Error', $data['title']);
                    self::assertSame('current', $data['detail']);
                    self::assertNull($data['instance']);
                    self::assertCount(2, $data['backtrace']);
                    self::assertSame('application/json', $contentType);

                    return 'encoded';
                }
            ),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [500, ''], $response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithCallback(
                'error',
                static function (string $message, array $context): void {
                    self::assertSame('Http Exception', $message);
                    self::assertArrayHasKey('backtrace', $context);

                    self::assertCount(2, $context['backtrace']);

                    $trace1 = array_shift($context['backtrace']);

                    self::assertSame(\LogicException::class, $trace1['class']);
                    self::assertSame('current', $trace1['message']);
                    self::assertSame(5, $trace1['code']);
                    self::assertMatchesRegularExpression('/ApiExceptionMiddlewareTest\.php/', $trace1['file']);

                    $trace2 = array_shift($context['backtrace']);

                    self::assertSame(\RuntimeException::class, $trace2['class']);
                    self::assertSame('previous', $trace2['message']);
                    self::assertSame(3, $trace2['code']);
                    self::assertMatchesRegularExpression('/ApiExceptionMiddlewareTest\.php/', $trace2['file']);
                }
            ),
        ]);

        $apiExceptionMiddleware = new ApiExceptionMiddleware($encoder, $responseFactory, true, $logger);

        self::assertSame($response, $apiExceptionMiddleware->process($request, $handler));
    }

    public function testWithoutDebugAndLoggerWithExceptionAndWithAccept(): void
    {
        $builder = new MockObjectBuilder();
        $previousException = new \RuntimeException('previous', 3);
        $exception = new \LogicException('current', 5, $previousException);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
        ]);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $builder->create(StreamInterface::class, [
            new WithReturn('write', ['encoded'], \strlen('encoded')),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf(
                'withHeader',
                ['Content-Type', 'application/problem+json']
            ),
            new WithReturn('getBody', [], $responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithException('handle', [$request], $exception),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $builder->create(EncoderInterface::class, [
            new WithCallback(
                'encode',
                static function (array $data, string $contentType): string {
                    self::assertSame('https://datatracker.ietf.org/doc/html/rfc2616#section-10.5.1', $data['type']);
                    self::assertSame(500, $data['status']);
                    self::assertSame('Internal Server Error', $data['title']);
                    self::assertNull($data['detail']);
                    self::assertNull($data['instance']);
                    self::assertArrayNotHasKey('backtrace', $data);
                    self::assertSame('application/json', $contentType);

                    return 'encoded';
                }
            ),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [500, ''], $response),
        ]);

        $apiExceptionMiddleware = new ApiExceptionMiddleware($encoder, $responseFactory);

        self::assertSame($response, $apiExceptionMiddleware->process($request, $handler));
    }

    public function testWithDebugAndLoggerWithHttpExceptionAndWithAccept(): void
    {
        $builder = new MockObjectBuilder();
        $previousException = new \RuntimeException('previous', 3);
        $httpException = HttpException::createBadRequest(['key' => 'value'], $previousException);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
        ]);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $builder->create(StreamInterface::class, [
            new WithReturn('write', ['encoded'], \strlen('encoded')),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf(
                'withHeader',
                ['Content-Type', 'application/problem+json']
            ),
            new WithReturn('getBody', [], $responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithException('handle', [$request], $httpException),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $builder->create(EncoderInterface::class, [
            new WithCallback(
                'encode',
                static function (array $data, string $contentType): string {
                    self::assertSame('https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.1', $data['type']);
                    self::assertSame(400, $data['status']);
                    self::assertSame('Bad Request', $data['title']);
                    self::assertNull($data['detail']);
                    self::assertNull($data['instance']);
                    self::assertSame('application/json', $contentType);

                    return 'encoded';
                }
            ),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [400, ''], $response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithCallback(
                'info',
                static function (string $message, array $context): void {
                    self::assertSame('Http Exception', $message);
                    self::assertArrayHasKey('backtrace', $context);

                    self::assertCount(2, $context['backtrace']);

                    $trace1 = array_shift($context['backtrace']);

                    self::assertSame(HttpException::class, $trace1['class']);
                    self::assertSame('Bad Request', $trace1['message']);
                    self::assertSame(400, $trace1['code']);
                    self::assertMatchesRegularExpression('/HttpException\.php/', $trace1['file']);

                    $trace2 = array_shift($context['backtrace']);

                    self::assertSame(\RuntimeException::class, $trace2['class']);
                    self::assertSame('previous', $trace2['message']);
                    self::assertSame(3, $trace2['code']);
                    self::assertMatchesRegularExpression('/ApiExceptionMiddlewareTest\.php/', $trace2['file']);
                }
            ),
        ]);

        $apiExceptionMiddleware = new ApiExceptionMiddleware($encoder, $responseFactory, true, $logger);

        self::assertSame($response, $apiExceptionMiddleware->process($request, $handler));
    }
}
