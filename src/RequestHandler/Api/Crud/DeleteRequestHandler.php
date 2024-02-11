<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Repository\RepositoryInterface;
use Chubbyphp\HttpException\HttpException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

final class DeleteRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private RepositoryInterface $repository,
        private ResponseFactoryInterface $responseFactory
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $accept = $request->getAttribute('accept');

        if (!Uuid::isValid($id) || null === $model = $this->repository->findById($id)) {
            throw HttpException::createNotFound();
        }

        $this->repository->remove($model);
        $this->repository->flush();

        return $this->responseFactory->createResponse(204)->withHeader('Content-Type', $accept);
    }
}
