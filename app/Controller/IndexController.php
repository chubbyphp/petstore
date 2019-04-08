<?php

declare(strict_types=1);

namespace App\Controller;

use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IndexController implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param UrlGeneratorInterface    $urlGenerator
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(200)
            ->withHeader('Location', $this->urlGenerator->generateUri($request, 'swagger_index'));
    }
}
