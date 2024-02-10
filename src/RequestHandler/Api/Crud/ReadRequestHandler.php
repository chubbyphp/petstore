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
        private ParsingInterface $parsing,
        private RepositoryInterface $repository,
        private EncoderInterface $encoder,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $accept = $request->getAttribute('accept');

        if (!Uuid::isValid($id) || null === $model = $this->repository->findById($id)) {
            throw HttpException::createNotFound();
        }

        $output = $this->encoder->encode($this->parsing->getModelResponseSchema($request)->parse($model), $accept);

        $response = $this->responseFactory->createResponse(200)->withHeader('Content-Type', $accept);
        $response->getBody()->write($output);

        return $response;
    }
}
