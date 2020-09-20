<?php

declare(strict_types=1);

namespace App\RequestHandler\Api;

use Chubbyphp\Serialization\SerializerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PingRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        SerializerInterface $serializer
    ) {
        $this->responseFactory = $responseFactory;
        $this->serializer = $serializer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getAttribute('accept');

        $body = $this->serializer->encode(['date' => date('c')], $accept);

        $response = $this->responseFactory->createResponse(200)
            ->withHeader('Content-Type', $accept)
            ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0')
        ;

        $response->getBody()->write($body);

        return $response;
    }
}
