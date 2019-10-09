<?php

declare(strict_types=1);

namespace App\RequestHandler\Crud;

use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotFound;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ReadRequestHandler implements RequestHandlerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var ResponseManagerInterface
     */
    private $responseManager;

    public function __construct(
        RepositoryInterface $repository,
        ResponseManagerInterface $responseManager
    ) {
        $this->repository = $repository;
        $this->responseManager = $responseManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $accept = $request->getAttribute('accept');

        if (null === $model = $this->repository->findById($id)) {
            return $this->responseManager->createFromApiProblem(new NotFound(), $accept);
        }

        $context = NormalizerContextBuilder::create()->setRequest($request)->getContext();

        return $this->responseManager->create($model, $accept, 200, $context);
    }
}
