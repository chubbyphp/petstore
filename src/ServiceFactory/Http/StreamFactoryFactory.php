<?php

declare(strict_types=1);

namespace App\ServiceFactory\Http;

use Slim\Psr7\Factory\StreamFactory;

final class StreamFactoryFactory
{
    public function __invoke(): StreamFactory
    {
        return new StreamFactory();
    }
}
