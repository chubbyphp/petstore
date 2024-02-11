<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Dto\Collection\CollectionRequestInterface;
use App\Parsing\ParsingInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Parsing\ParserErrorException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ListRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private ParsingInterface $parsing,
        private RepositoryInterface $repository,
        private EncoderInterface $encoder,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getAttribute('accept');

        $input = $request->getQueryParams();

        try {
            /** @var CollectionRequestInterface $collectionRequest */
            $collectionRequest = $this->parsing->getCollectionRequestSchema($request)->parse($input);
        } catch (ParserErrorException $e) {
            throw HttpException::createBadRequest(['invalidParameters' => $e->getApiProblemErrorMessages()]);
        }

        $collection = $collectionRequest->createCollection();

        $this->repository->resolveCollection($collection);

        $output = $this->encoder->encode(
            $this->parsing->getCollectionResponseSchema($request)->parse($collection),
            $accept
        );

        $response = $this->responseFactory->createResponse(200)->withHeader('Content-Type', $accept);
        $response->getBody()->write($output);

        return $response;
    }
}
