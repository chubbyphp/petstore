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
    public function __construct(
        private CollectionFactoryInterface $factory,
        private RepositoryInterface $repository,
        private RequestManagerInterface $requestManager,
        private ResponseManagerInterface $responseManager,
        private ValidatorInterface $validator
    ) {
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
