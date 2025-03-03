<?php

declare(strict_types=1);

namespace App\Tests\Unit\Middleware;

use App\Middleware\ConvertHttpExceptionMiddleware;
use Chubbyphp\HttpException\HttpException as ChubbyphpHttpException;
use Chubbyphp\Mock\MockMethod\WithException;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testMiddleware(): void
    {
        $builder = new MockObjectBuilder();

        $chubbyphpHttpException = ChubbyphpHttpException::createBadRequest(['key' => 'value']);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithException('handle', [$request], $chubbyphpHttpException),
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
