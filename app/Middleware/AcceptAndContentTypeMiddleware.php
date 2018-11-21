<?php

declare(strict_types=1);

namespace App\Middleware;

use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AcceptAndContentTypeMiddleware
{
    /**
     * @var AcceptNegotiatorInterface
     */
    private $acceptNegotiator;

    /**
     * @var ContentTypeNegotiatorInterface
     */
    private $contentTypeNegotiator;

    /**
     * @var ResponseManagerInterface
     */
    private $responseManager;

    /**
     * @param AcceptNegotiatorInterface      $acceptNegotiator
     * @param ContentTypeNegotiatorInterface $contentTypeNegotiator
     * @param ResponseManagerInterface       $responseManager
     */
    public function __construct(
        AcceptNegotiatorInterface $acceptNegotiator,
        ContentTypeNegotiatorInterface $contentTypeNegotiator,
        ResponseManagerInterface $responseManager
    ) {
        $this->acceptNegotiator = $acceptNegotiator;
        $this->contentTypeNegotiator = $contentTypeNegotiator;
        $this->responseManager = $responseManager;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        if (null === $accept = $this->acceptNegotiator->negotiate($request)) {
            return $this->responseManager->createAcceptNotSupported($request->getHeaderLine('Accept'));
        }

        $request = $request->withAttribute('accept', $accept->getValue());

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            if (null === $contentType = $this->contentTypeNegotiator->negotiate($request)) {
                return $this->responseManager->createContentTypeNotSupported(
                    $request->getHeaderLine('Content-Type'),
                    $accept->getValue()
                );
            }

            $request = $request->withAttribute('contentType', $contentType->getValue());
        }

        return $next($request, $response);
    }
}
