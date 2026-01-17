<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Dto\Model\ModelRequestInterface as ModelModelRequestInterface;
use App\Dto\Model\ModelResponseInterface;
use App\Parsing\ParsingInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Parsing\ErrorsException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

final class UpdateRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly DecoderInterface $decoder,
        private readonly ParsingInterface $parsing,
        private readonly RepositoryInterface $repository,
        private readonly EncoderInterface $encoder,
        private readonly ResponseFactoryInterface $responseFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var string $id */
        $id = $request->getAttribute('id');

        /** @var string $accept */
        $accept = $request->getAttribute('accept');

        /** @var string $contentType */
        $contentType = $request->getAttribute('contentType');

        if (!Uuid::isValid($id) || null === $model = $this->repository->findById($id)) {
            throw HttpException::createNotFound();
        }

        $input = $this->decoder->decode((string) $request->getBody(), $contentType);

        try {
            /** @var ModelModelRequestInterface $modelRequest */
            $modelRequest = $this->parsing->getModelRequestSchema($request)->parse($input);
        } catch (ErrorsException $e) {
            throw HttpException::createUnprocessableEntity([
                'invalidParameters' => $e->errors->toApiProblemInvalidParameters(),
            ]);
        }

        $model = $modelRequest->updateModel($model);

        $this->repository->persist($model);
        $this->repository->flush();

        /** @var ModelResponseInterface $modelResponse */
        $modelResponse = $this->parsing->getModelResponseSchema($request)->parse($model);

        $output = $this->encoder->encode($modelResponse->jsonSerialize(), $accept);

        $response = $this->responseFactory->createResponse(200)->withHeader('Content-Type', $accept);
        $response->getBody()->write($output);

        return $response;
    }
}
