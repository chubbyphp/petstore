<?php

declare(strict_types=1);

namespace App\Tests\Unit\Middleware;

use App\Middleware\ConvertHttpExceptionMiddleware;
use Chubbyphp\HttpException\HttpException as ChubbyphpHttpException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException as SlimHttpException;

/**
 * @covers \App\Middleware\ConvertHttpExceptionMiddleware
 *
 * @internal
 */
final class ConvertHttpExceptionMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testMiddlware(): void
    {
        $chubbyphpHttpException = ChubbyphpHttpException::createBadRequest(['key' => 'value']);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willThrowException($chubbyphpHttpException),
        ]);

        $convertHttpExceptionMiddleware = new ConvertHttpExceptionMiddleware();

        try {
            $convertHttpExceptionMiddleware->process($request, $handler);

            throw new \Exception('Expect fail');
        } catch (\Exception $e) {
            self::assertInstanceOf(SlimHttpException::class, $e);
        }
    }
}
