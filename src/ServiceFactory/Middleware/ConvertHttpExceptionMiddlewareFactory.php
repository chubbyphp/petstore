<?php

declare(strict_types=1);

namespace App\ServiceFactory\Middleware;

use App\Middleware\ConvertHttpExceptionMiddleware;

final class ConvertHttpExceptionMiddlewareFactory
{
    public function __invoke(): ConvertHttpExceptionMiddleware
    {
        return new ConvertHttpExceptionMiddleware();
    }
}
