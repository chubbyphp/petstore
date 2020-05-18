<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Security\Authentication\AuthenticationMiddleware;
use App\Security\Authentication\SessionAuthentication;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\ApiHttp\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Psr\Container\ContainerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

final class MiddlewareServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            AcceptAndContentTypeMiddleware::class => static function (ContainerInterface $container) {
                return new AcceptAndContentTypeMiddleware(
                    $container->get('negotiator.acceptNegotiator'),
                    $container->get('negotiator.contentTypeNegotiator'),
                    $container->get('api-http.response.manager')
                );
            },
            ApiExceptionMiddleware::class => static function (ContainerInterface $container) {
                return new ApiExceptionMiddleware(
                    $container->get('api-http.response.manager'),
                    $container->get('debug'),
                    $container->get('logger')
                );
            },
            CorsMiddleware::class => static function (ContainerInterface $container) {
                $cors = $container->get('cors');

                $allowOrigins = [];
                foreach ($cors['allow-origin'] as $allowOrigin => $class) {
                    $allowOrigins[] = new $class($allowOrigin);
                }

                return new CorsMiddleware(
                    $container->get('api-http.response.factory'),
                    new OriginNegotiator($allowOrigins),
                    new MethodNegotiator($cors['allow-methods']),
                    new HeadersNegotiator($cors['allow-headers']),
                    $cors['expose-headers'],
                    $cors['allow-credentials'],
                    $cors['max-age']
                );
            },
            SessionMiddleware::class => static function (ContainerInterface $container) {
                $sessionConfig = $container->get('session');

                return SessionMiddleware::fromAsymmetricKeyDefaults(
                    $sessionConfig['privateKey'],
                    $sessionConfig['publicKey'],
                    $sessionConfig['expirationTime']
                );
            },
            AuthenticationMiddleware::class => static function (ContainerInterface $container) {
                return new AuthenticationMiddleware([
                    $container->get(SessionAuthentication::class),
                ], $container->get('api-http.response.manager'));
            },
        ];
    }
}
