<?php

declare(strict_types=1);

namespace App\Tests\Unit\Middleware;

use App\Middleware\ApiExceptionMiddleware;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testWithDebugAndLoggerWithoutException(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        $apiExceptionMiddleware = new ApiExceptionMiddleware($encoder, $responseFactory, true, $logger);

        self::assertSame($response, $apiExceptionMiddleware->process($request, $handler));
    }

    public function testWithDebugAndLoggerWithExceptionAndWithoutAccept(): void
    {
        $previousException = new \RuntimeException('previous', 3);
        $exception = new \LogicException('current', 5, $previousException);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn(null),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willThrowException($exception),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('error')->with('Http Exception', new ArgumentCallback(static function (array $context): void {
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
            })),
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
        $previousException = new \RuntimeException('previous', 3);
        $exception = new \LogicException('current', 5, $previousException);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with('encoded'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/problem+json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willThrowException($exception),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class, [
            Call::create('encode')->with(new ArgumentCallback(static function (array $data): void {
                self::assertSame('https://datatracker.ietf.org/doc/html/rfc2616#section-10.5.1', $data['type']);
                self::assertSame(500, $data['status']);
                self::assertSame('Internal Server Error', $data['title']);
                self::assertSame('current', $data['detail']);
                self::assertNull($data['instance']);
                self::assertCount(2, $data['backtrace']);
            }), 'application/json')->willReturn('encoded'),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('error')->with('Http Exception', new ArgumentCallback(static function (array $context): void {
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
            })),
        ]);

        $apiExceptionMiddleware = new ApiExceptionMiddleware($encoder, $responseFactory, true, $logger);

        self::assertSame($response, $apiExceptionMiddleware->process($request, $handler));
    }

    public function testWithoutDebugAndLoggerWithExceptionAndWithAccept(): void
    {
        $previousException = new \RuntimeException('previous', 3);
        $exception = new \LogicException('current', 5, $previousException);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with('encoded'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/problem+json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willThrowException($exception),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class, [
            Call::create('encode')->with(new ArgumentCallback(static function (array $data): void {
                self::assertSame('https://datatracker.ietf.org/doc/html/rfc2616#section-10.5.1', $data['type']);
                self::assertSame(500, $data['status']);
                self::assertSame('Internal Server Error', $data['title']);
                self::assertNull($data['detail']);
                self::assertNull($data['instance']);
                self::assertArrayNotHasKey('backtrace', $data);
            }), 'application/json')->willReturn('encoded'),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        $apiExceptionMiddleware = new ApiExceptionMiddleware($encoder, $responseFactory);

        self::assertSame($response, $apiExceptionMiddleware->process($request, $handler));
    }

    public function testWithDebugAndLoggerWithHttpExceptionAndWithAccept(): void
    {
        $previousException = new \RuntimeException('previous', 3);
        $httpException = HttpException::createBadRequest(['key' => 'value'], $previousException);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with('encoded'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/problem+json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willThrowException($httpException),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class, [
            Call::create('encode')->with(new ArgumentCallback(static function (array $data): void {
                self::assertSame('https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.1', $data['type']);
                self::assertSame(400, $data['status']);
                self::assertSame('Bad Request', $data['title']);
                self::assertNull($data['detail']);
                self::assertNull($data['instance']);
            }), 'application/json')->willReturn('encoded'),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(400, '')->willReturn($response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('Http Exception', new ArgumentCallback(static function (array $context): void {
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
            })),
        ]);

        $apiExceptionMiddleware = new ApiExceptionMiddleware($encoder, $responseFactory, true, $logger);

        self::assertSame($response, $apiExceptionMiddleware->process($request, $handler));
    }
}
