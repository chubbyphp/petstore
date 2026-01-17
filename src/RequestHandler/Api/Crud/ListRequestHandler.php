<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Dto\Collection\CollectionRequestInterface;
use App\Dto\Collection\CollectionResponseInterface;
use App\Parsing\ParsingInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Parsing\ErrorsException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ListRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ParsingInterface $parsing,
        private readonly RepositoryInterface $repository,
        private readonly EncoderInterface $encoder,
        private readonly ResponseFactoryInterface $responseFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var string $accept */
        $accept = $request->getAttribute('accept');

        $input = $request->getQueryParams();

        try {
            /** @var CollectionRequestInterface $collectionRequest */
            $collectionRequest = $this->parsing->getCollectionRequestSchema($request)->parse($input);
        } catch (ErrorsException $e) {
            throw HttpException::createBadRequest([
                'invalidParameters' => $e->errors->toApiProblemInvalidParameters(),
            ]);
        }

        $collection = $collectionRequest->createCollection();

        $this->repository->resolveCollection($collection);

        /** @var CollectionResponseInterface $collectionResponse */
        $collectionResponse = $this->parsing->getCollectionResponseSchema($request)->parse($collection);

        $output = $this->encoder->encode($collectionResponse->jsonSerialize(), $accept);

        $response = $this->responseFactory->createResponse(200)->withHeader('Content-Type', $accept);
        $response->getBody()->write($output);

        return $response;
    }
}
