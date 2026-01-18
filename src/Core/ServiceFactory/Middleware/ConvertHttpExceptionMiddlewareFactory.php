<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Middleware;

use App\Core\Middleware\ConvertHttpExceptionMiddleware;

final class ConvertHttpExceptionMiddlewareFactory
{
    public function __invoke(): ConvertHttpExceptionMiddleware
    {
        return new ConvertHttpExceptionMiddleware();
    }
}
