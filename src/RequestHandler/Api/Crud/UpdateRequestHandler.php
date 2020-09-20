<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotFound;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnprocessableEntity;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextBuilder;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Chubbyphp\Validation\Error\ApiProblemErrorMessages;
use Chubbyphp\Validation\Error\ErrorInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

final class UpdateRequestHandler implements RequestHandlerInterface
{
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
        RepositoryInterface $repository,
        RequestManagerInterface $requestManager,
        ResponseManagerInterface $responseManager,
        ValidatorInterface $validator
    ) {
        $this->repository = $repository;
        $this->requestManager = $requestManager;
        $this->responseManager = $responseManager;
        $this->validator = $validator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $accept = $request->getAttribute('accept');
        $contentType = $request->getAttribute('contentType');

        if (!Uuid::isValid($id) || null === $model = $this->repository->findById($id)) {
            /** @var ModelInterface $model */
            return $this->responseManager->createFromApiProblem(new NotFound(), $accept);
        }

        /** @var ModelInterface $model */
        $model = $this->requestManager->getDataFromRequestBody(
            $request,
            $model,
            $contentType,
            $this->getDenormalizerContext()
        );

        if ([] !== $errors = $this->validator->validate($model)) {
            return $this->createValidationErrorResponse($errors, $accept);
        }

        $model->setUpdatedAt(new \DateTime());

        $this->repository->persist($model);
        $this->repository->flush();

        return $this->responseManager->create($model, $accept, 200, $this->getNormalizerContext($request));
    }

    private function getDenormalizerContext(): DenormalizerContextInterface
    {
        return DenormalizerContextBuilder::create()
            ->setAllowedAdditionalFields(['id', 'createdAt', 'updatedAt', '_links'])
            ->setClearMissing(true)
            ->getContext()
        ;
    }

    /**
     * @param array<ErrorInterface> $errors
     */
    private function createValidationErrorResponse(array $errors, string $accept): ResponseInterface
    {
        return $this->responseManager->createFromApiProblem(
            new UnprocessableEntity((new ApiProblemErrorMessages($errors))->getMessages()),
            $accept
        );
    }

    private function getNormalizerContext(ServerRequestInterface $request): NormalizerContextInterface
    {
        return NormalizerContextBuilder::create()->setRequest($request)->getContext();
    }
}
