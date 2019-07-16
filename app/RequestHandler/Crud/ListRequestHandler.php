<?php

declare(strict_types=1);

namespace App\RequestHandler\Crud;

use App\ApiHttp\Factory\InvalidParametersFactoryInterface;
use App\Factory\CollectionFactoryInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextBuilder;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\BadRequest;

final class ListRequestHandler implements RequestHandlerInterface
{
    /**
     * @var InvalidParametersFactoryInterface
     */
    private $errorFactory;

    /**
     * @var CollectionFactoryInterface
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
     * @param InvalidParametersFactoryInterface $errorFactory
     * @param CollectionFactoryInterface        $factory
     * @param RepositoryInterface               $repository
     * @param RequestManagerInterface           $requestManager
     * @param ResponseManagerInterface          $responseManager
     * @param ValidatorInterface                $validator
     */
    public function __construct(
        InvalidParametersFactoryInterface $errorFactory,
        CollectionFactoryInterface $factory,
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

        $collection = $this->requestManager->getDataFromRequestQuery($request, $this->factory->create());

        if ([] !== $errors = $this->validator->validate($collection)) {
            return $this->responseManager->createFromApiProblem(
                new BadRequest($this->errorFactory->createInvalidParameters($errors)),
                $accept
            );
        }

        $this->repository->resolveCollection($collection);

        $context = NormalizerContextBuilder::create()->setRequest($request)->getContext();

        return $this->responseManager->create($collection, $accept, 200, $context);
    }
}
