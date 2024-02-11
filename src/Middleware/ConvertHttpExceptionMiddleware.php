<?php

declare(strict_types=1);

namespace App\Middleware;

use Chubbyphp\HttpException\HttpException as ChubbyphpHttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException as SlimHttpException;

final class ConvertHttpExceptionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ChubbyphpHttpException $chubbyphpHttpException) {
            throw new SlimHttpException(
                $request,
                $chubbyphpHttpException->getMessage(),
                $chubbyphpHttpException->getStatus(),
                $chubbyphpHttpException
            );
        }
    }
}
