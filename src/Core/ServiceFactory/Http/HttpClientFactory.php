<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Http;

use GuzzleHttp\Client;

final class HttpClientFactory
{
    public function __invoke(): Client
    {
        // PSR-18 has no per-request timeout concept, so the timeouts are configured on the client itself; redirects
        // are disabled: neither the oidc discovery nor the jwks endpoint should redirect, and a client following a
        // https -> http redirect on its own would silently downgrade those fetches
        return new Client([
            'timeout' => 5,
            'connect_timeout' => 2,
            'allow_redirects' => false,
        ]);
    }
}
