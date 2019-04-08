<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use App\ApiHttp\Factory\ErrorFactoryInterface;
use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Serialization\Normalizer\NormalizerContextBuilder;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class UpdateController implements RequestHandlerInterface
{
    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

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
     * @param RepositoryInterface      $repository
     * @param RequestManagerInterface  $requestManager
     * @param ResponseManagerInterface $responseManager
     * @param ValidatorInterface       $validator
     */
    public function __construct(
        ErrorFactoryInterface $errorFactory,
        RepositoryInterface $repository,
        RequestManagerInterface $requestManager,
        ResponseManagerInterface $responseManager,
        ValidatorInterface $validator
    ) {
        $this->errorFactory = $errorFactory;
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
        $id = $request->getAttribute('id');
        $accept = $request->getAttribute('accept');
        $contentType = $request->getAttribute('contentType');

        /** @var ModelInterface $model */
        if (null === $model = $this->repository->findById($id)) {
            return $this->responseManager->createResourceNotFound(['model' => $id], $accept);
        }

        $model->reset();

        $context = DenormalizerContextBuilder::create()
            ->setAllowedAdditionalFields(['id', 'createdAt', 'updatedAt', '_links'])
            ->getContext();

        $model = $this->requestManager->getDataFromRequestBody($request, $model, $contentType, $context);

        if ([] !== $errors = $this->validator->validate($model)) {
            return $this->responseManager->createFromError(
                $this->errorFactory->createFromValidationError(ErrorInterface::SCOPE_BODY, $errors),
                $accept,
                422
            );
        }

        $model->setUpdatedAt(new \DateTime());

        $this->repository->persist($model);
        $this->repository->flush();

        $context = NormalizerContextBuilder::create()->setRequest($request)->getContext();

        return $this->responseManager->create($model, $accept, 200, $context);
    }
}
