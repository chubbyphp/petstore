<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotFound;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

final class DeleteRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private RepositoryInterface $repository,
        private ResponseManagerInterface $responseManager
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $accept = $request->getAttribute('accept');

        if (!Uuid::isValid($id) || null === $model = $this->repository->findById($id)) {
            return $this->responseManager->createFromApiProblem(new NotFound(), $accept);
        }

        $this->repository->remove($model);
        $this->repository->flush();

        return $this->responseManager->createEmpty($accept);
    }
}
