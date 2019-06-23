<?php

declare(strict_types=1);

namespace App\ApiHttp\Factory;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;

final class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * @param int    $code
     * @param string $reasonPhrase
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $response = new Response($code);

        if ('' !== $reasonPhrase) {
            $response = $response->withStatus($code, $reasonPhrase);
        }

        return $response;
    }
}
