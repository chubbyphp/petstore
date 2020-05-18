<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use Chubbyphp\ApiHttp\ApiProblem\ClientError\Unauthorized;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @var array<string, AuthenticationInterface>
     */
    private $authentications;

    /**
     * @var ResponseManagerInterface
     */
    private $responseManager;

    /**
     * @param array<int, AuthenticationInterface> $authentications
     */
    public function __construct(array $authentications, ResponseManagerInterface $responseManager)
    {
        $this->authentications = [];
        foreach ($authentications as $authentication) {
            $this->addAuthentication($authentication);
        }

        $this->responseManager = $responseManager;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $accept = $request->getAttribute('accept');

        foreach ($this->authentications as $type => $authentication) {
            if (!$authentication->isResponsible($request)) {
                continue;
            }

            if (!$authentication->isAuthenticated($request)) {
                return $this->responseManager->createFromApiProblem(
                    new Unauthorized($type, array_keys($this->authentications)),
                    $accept
                );
            }

            return $handler->handle($request);
        }

        return $this->responseManager->createFromApiProblem(
            new Unauthorized('unknown', array_keys($this->authentications)),
            $accept
        );
    }

    private function addAuthentication(AuthenticationInterface $authentication): void
    {
        $this->authentications[$authentication->getType()] = $authentication;
    }
}
