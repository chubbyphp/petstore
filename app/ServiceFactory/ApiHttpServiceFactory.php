<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

final class ApiHttpServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'api-http.response.factory' => static function () {
                return new ResponseFactory();
            },
            'api-http.stream.factory' => static function () {
                return new StreamFactory();
            },
        ];
    }
}
