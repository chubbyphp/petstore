<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Parsing\ParsingInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

final class ReadRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ParsingInterface $parsing,
        private readonly RepositoryInterface $repository,
        private readonly EncoderInterface $encoder,
        private readonly ResponseFactoryInterface $responseFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var string $id */
        $id = $request->getAttribute('id');

        /** @var string $accept */
        $accept = $request->getAttribute('accept');

        if (!Uuid::isValid($id) || null === $model = $this->repository->findById($id)) {
            throw HttpException::createNotFound();
        }

        /** @var array<string, mixed> $responseData */
        $responseData = $this->parsing->getModelResponseSchema($request)->parse($model);

        $output = $this->encoder->encode($responseData, $accept);

        $response = $this->responseFactory->createResponse(200)->withHeader('Content-Type', $accept);
        $response->getBody()->write($output);

        return $response;
    }
}
