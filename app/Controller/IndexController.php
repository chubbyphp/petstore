<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Router;

class IndexController implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param routerInterface          $router
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        Router $router
    ) {
        $this->responseFactory = $responseFactory;
        $this->router = $router;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', $this->router->pathFor('swagger_index'));
    }
}
