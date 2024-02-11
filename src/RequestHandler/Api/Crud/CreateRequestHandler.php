<?php

declare(strict_types=1);

namespace App\RequestHandler\Api\Crud;

use App\Dto\Model\ModelRequestInterface;
use App\Parsing\ParsingInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Parsing\ParserErrorException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CreateRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private DecoderInterface $decoder,
        private ParsingInterface $parsing,
        private RepositoryInterface $repository,
        private EncoderInterface $encoder,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getAttribute('accept');
        $contentType = $request->getAttribute('contentType');

        $input = $this->decoder->decode((string) $request->getBody(), $contentType);

        try {
            /** @var ModelRequestInterface $modelRequest */
            $modelRequest = $this->parsing->getModelRequestSchema($request)->parse($input);
        } catch (ParserErrorException $e) {
            throw HttpException::createUnprocessableEntity(['invalidParameters' => $e->getApiProblemErrorMessages()]);
        }

        $model = $modelRequest->createModel();

        $this->repository->persist($model);
        $this->repository->flush();

        $output = $this->encoder->encode($this->parsing->getModelResponseSchema($request)->parse($model), $accept);

        $response = $this->responseFactory->createResponse(201)->withHeader('Content-Type', $accept);
        $response->getBody()->write($output);

        return $response;
    }
}
