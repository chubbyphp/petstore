<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use App\ApiHttp\Factory\ErrorFactoryInterface;
use App\Factory\Model\FactoryInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CreateController
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
        $contentType = $request->getAttribute('contentType');

        $model = $this->requestManager->getDataFromRequestBody($request, $this->factory->create(), $contentType);

        if ([] !== $errors = $this->validator->validate($model)) {
            return $this->responseManager->createFromError(
                $this->errorFactory->createFromValidationError(ErrorInterface::SCOPE_BODY, $errors),
                $accept,
                422
            );
        }

        $this->repository->persist($model);
        $this->repository->flush();

        return $this->responseManager->create($model, $accept, 201);
    }
}
