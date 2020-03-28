<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Factory\ModelFactoryInterface;
use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnprocessableEntity;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextBuilder;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Chubbyphp\Validation\Error\ApiProblemErrorMessages;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CreateRequestHandler implements RequestHandlerInterface
{
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

    public function __construct(
        ModelFactoryInterface $factory,
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
        $contentType = $request->getAttribute('contentType');

        /** @var ModelInterface $model */
        $model = $this->requestManager->getDataFromRequestBody(
            $request,
            $this->factory->create(),
            $contentType,
            $this->getDenormalizerContext()
        );

        if ([] !== $errors = $this->validator->validate($model)) {
            return $this->responseManager->createFromApiProblem(
                new UnprocessableEntity((new ApiProblemErrorMessages($errors))->getMessages()),
                $accept
            );
        }

        $this->repository->persist($model);
        $this->repository->flush();

        return $this->responseManager->create($model, $accept, 201, $this->getNormalizerContext($request));
    }

    private function getDenormalizerContext(): DenormalizerContextInterface
    {
        return DenormalizerContextBuilder::create()->setClearMissing(true)->getContext();
    }

    private function getNormalizerContext(ServerRequestInterface $request): NormalizerContextInterface
    {
        return NormalizerContextBuilder::create()->setRequest($request)->getContext();
    }
}
