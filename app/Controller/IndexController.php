<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouterInterface;

class IndexController implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param RouterInterface          $router
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        RouterInterface $router
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
