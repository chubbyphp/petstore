<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Slim\Psr7\Factory\ServerRequestFactory as BaseFactory;

final class ServerRequestFactory
{
    public function __invoke(): \Closure
    {
        return static function () {
            return BaseFactory::createFromGlobals();
        };
    }
}
