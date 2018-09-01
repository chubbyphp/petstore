<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ReadController
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var ResponseManagerInterface
     */
    private $responseManager;

    /**
     * @param RepositoryInterface      $repository
     * @param ResponseManagerInterface $responseManager
     */
    public function __construct(
        RepositoryInterface $repository,
        ResponseManagerInterface $responseManager
    ) {
        $this->repository = $repository;
        $this->responseManager = $responseManager;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $accept = $request->getAttribute('accept');

        if (null === $model = $this->repository->findById($id)) {
            return $this->responseManager->createResourceNotFound(['model' => $id], $accept);
        }

        return $this->responseManager->create($model, $accept);
    }
}
