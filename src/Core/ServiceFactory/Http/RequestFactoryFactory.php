<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Http;

use Slim\Psr7\Factory\RequestFactory;

final class RequestFactoryFactory
{
    public function __invoke(): RequestFactory
    {
        return new RequestFactory();
    }
}
