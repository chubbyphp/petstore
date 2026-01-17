<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Dto\Model\ModelRequestInterface;
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

final class CreateRequestHandler implements RequestHandlerInterface
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
        /** @var string $accept */
        $accept = $request->getAttribute('accept');

        /** @var string $contentType */
        $contentType = $request->getAttribute('contentType');

        $input = $this->decoder->decode((string) $request->getBody(), $contentType);

        try {
            /** @var ModelRequestInterface $modelRequest */
            $modelRequest = $this->parsing->getModelRequestSchema($request)->parse($input);
        } catch (ErrorsException $e) {
            throw HttpException::createUnprocessableEntity([
                'invalidParameters' => $e->errors->toApiProblemInvalidParameters(),
            ]);
        }

        $model = $modelRequest->createModel();

        $this->repository->persist($model);
        $this->repository->flush();

        /** @var ModelResponseInterface $modelResponse */
        $modelResponse = $this->parsing->getModelResponseSchema($request)->parse($model);

        $output = $this->encoder->encode($modelResponse->jsonSerialize(), $accept);

        $response = $this->responseFactory->createResponse(201)->withHeader('Content-Type', $accept);
        $response->getBody()->write($output);

        return $response;
    }
}
