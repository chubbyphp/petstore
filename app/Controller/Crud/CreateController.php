<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use App\ApiHttp\Factory\ErrorFactoryInterface;
use App\Factory\ModelFactoryInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextBuilder;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnprocessableEntity;

final class CreateController implements RequestHandlerInterface
{
    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    /**
     * @var ModelFactoryInterface
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
     * @param ModelFactoryInterface    $factory
     * @param RepositoryInterface      $repository
     * @param RequestManagerInterface  $requestManager
     * @param ResponseManagerInterface $responseManager
     * @param ValidatorInterface       $validator
     */
    public function __construct(
        ErrorFactoryInterface $errorFactory,
        ModelFactoryInterface $factory,
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
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getAttribute('accept');
        $contentType = $request->getAttribute('contentType');

        $model = $this->requestManager->getDataFromRequestBody($request, $this->factory->create(), $contentType);

        if ([] !== $errors = $this->validator->validate($model)) {
            return $this->responseManager->createFromApiProblem(
                new UnprocessableEntity($this->errorFactory->createErrorMessages($errors), 'Validation error'),
                $accept
            );
        }

        $this->repository->persist($model);
        $this->repository->flush();

        $context = NormalizerContextBuilder::create()->setRequest($request)->getContext();

        return $this->responseManager->create($model, $accept, 201, $context);
    }
}
