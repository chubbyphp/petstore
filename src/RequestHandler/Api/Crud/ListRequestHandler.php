<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Collection\CollectionInterface;
use App\Factory\CollectionFactoryInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\BadRequest;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextBuilder;
use Chubbyphp\Validation\Error\ApiProblemErrorMessages;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ListRequestHandler implements RequestHandlerInterface
{
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

    public function __construct(
        CollectionFactoryInterface $factory,
        RepositoryInterface $repository,
        RequestManagerInterface $requestManager,
        ResponseManagerInterface $responseManager,
        ValidatorInterface $validator
    ) {
        $this->factory = $factory;
        $this->repository = $repository;
        $this->requestManager = $requestManager;
        $this->responseManager = $responseManager;
        $this->validator = $validator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getAttribute('accept');

        /** @var CollectionInterface $collection */
        $collection = $this->requestManager->getDataFromRequestQuery($request, $this->factory->create());

        if ([] !== $errors = $this->validator->validate($collection)) {
            return $this->responseManager->createFromApiProblem(
                new BadRequest((new ApiProblemErrorMessages($errors))->getMessages()),
                $accept
            );
        }

        $this->repository->resolveCollection($collection);

        $context = NormalizerContextBuilder::create()->setRequest($request)->getContext();

        return $this->responseManager->create($collection, $accept, 200, $context);
    }
}
