<?php

declare(strict_types=1);

namespace App\RequestHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteParserInterface;

class IndexRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var RouteParserInterface
     */
    private $router;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param RouteParserInterface     $router
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        RouteParserInterface $router
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
            ->withHeader('Location', $this->router->urlFor('swagger_index'))
        ;
    }
}
