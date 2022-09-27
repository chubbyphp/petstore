<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\HttpException\HttpException;
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
    public function __construct(
        private RepositoryInterface $repository,
        private RequestManagerInterface $requestManager,
        private ResponseManagerInterface $responseManager,
        private ValidatorInterface $validator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $accept = $request->getAttribute('accept');
        $contentType = $request->getAttribute('contentType');

        if (!Uuid::isValid($id) || null === $model = $this->repository->findById($id)) {
            return $this->responseManager->createFromHttpException(
                HttpException::createNotFound(),
                $accept
            );
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

        $model->setUpdatedAt(new \DateTimeImmutable());

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
        return $this->responseManager->createFromHttpException(
            HttpException::createUnprocessableEntity(['invalidParameters' => (new ApiProblemErrorMessages($errors))->getMessages(),
            ]),
            $accept
        );
    }

    private function getNormalizerContext(ServerRequestInterface $request): NormalizerContextInterface
    {
        return NormalizerContextBuilder::create()->setRequest($request)->getContext();
    }
}
