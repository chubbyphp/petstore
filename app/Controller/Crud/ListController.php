<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use App\ApiHttp\Factory\ErrorFactoryInterface;
use App\Factory\Collection\FactoryInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ListController
{
    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var RequestManagerInterface
     */
    private $requestManager;

    /**
     * @var ResponseManagerInterface
     */
    private $responseManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ErrorFactoryInterface    $errorFactory
     * @param FactoryInterface         $factory
     * @param RepositoryInterface      $repository
     * @param RequestManagerInterface  $requestManager
     * @param ResponseManagerInterface $responseManager
     * @param ValidatorInterface       $validator
     */
    public function __construct(
        ErrorFactoryInterface $errorFactory,
        FactoryInterface $factory,
        RepositoryInterface $repository,
        RequestManagerInterface $requestManager,
        ResponseManagerInterface $responseManager,
        ValidatorInterface $validator
    ) {
        $this->errorFactory = $errorFactory;
        $this->factory = $factory;
        $this->repository = $repository;
        $this->requestManager = $requestManager;
        $this->responseManager = $responseManager;
        $this->validator = $validator;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getAttribute('accept');

        $collection = $this->requestManager->getDataFromRequestQuery($request, $this->factory->create());

        if ([] !== $errors = $this->validator->validate($collection)) {
            return $this->responseManager->createFromError(
                $this->errorFactory->createFromValidationError(ErrorInterface::SCOPE_QUERY, $errors),
                $accept
            );
        }

        $this->repository->resolveCollection($collection);

        return $this->responseManager->create($collection, $accept);
    }
}
