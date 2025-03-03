<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Api\Crud;

use App\Dto\Model\ModelRequestInterface;
use App\Model\ModelInterface;
use App\Parsing\ParsingInterface;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpExceptionInterface;
use Chubbyphp\Mock\MockMethod\WithException;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Parsing\ParserErrorException;
use Chubbyphp\Parsing\Schema\ObjectSchemaInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \App\RequestHandler\Api\Crud\UpdateRequestHandler
 *
 * @internal
 */
final class UpdateRequestHandlerTest extends TestCase
{
    public function testResourceNotFoundInvalidUuid(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['id', null], '1234'),
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
            new WithReturn('getAttribute', ['contentType', null], 'application/json'),
        ]);

        /** @var DecoderInterface $decoder */
        $decoder = $builder->create(DecoderInterface::class, []);

        /** @var ParsingInterface $parsing */
        $parsing = $builder->create(ParsingInterface::class, []);

        /** @var RepositoryInterface $repository */
        $repository = $builder->create(RepositoryInterface::class, []);

        /** @var EncoderInterface $encoder */
        $encoder = $builder->create(EncoderInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $requestHandler = new UpdateRequestHandler(
            $decoder,
            $parsing,
            $repository,
            $encoder,
            $responseFactory
        );

        try {
            $requestHandler->handle($request);

            throw new \Exception('Expected Exception');
        } catch (HttpExceptionInterface $e) {
            self::assertSame(404, $e->getStatus());
        }
    }

    public function testResourceNotFoundMissingModel(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['id', null], 'cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
            new WithReturn('getAttribute', ['contentType', null], 'application/json'),
        ]);

        /** @var DecoderInterface $decoder */
        $decoder = $builder->create(DecoderInterface::class, []);

        /** @var ParsingInterface $parsing */
        $parsing = $builder->create(ParsingInterface::class, []);

        /** @var RepositoryInterface $repository */
        $repository = $builder->create(RepositoryInterface::class, [
            new WithReturn('findById', ['cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'], null),
        ]);

        /** @var EncoderInterface $encoder */
        $encoder = $builder->create(EncoderInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $requestHandler = new UpdateRequestHandler(
            $decoder,
            $parsing,
            $repository,
            $encoder,
            $responseFactory
        );

        try {
            $requestHandler->handle($request);

            throw new \Exception('Expected Exception');
        } catch (HttpExceptionInterface $e) {
            self::assertInstanceOf(HttpExceptionInterface::class, $e);
            self::assertSame(404, $e->getStatus());
        }
    }

    public function testWithParsingError(): void
    {
        $parserErrorException = new ParserErrorException();

        $inputAsStdClass = new \stdClass();
        $inputAsStdClass->name = 'test';
        $inputAsArray = (array) $inputAsStdClass;
        $inputAsJson = json_encode($inputAsArray);

        $builder = new MockObjectBuilder();

        /** @var StreamInterface $requestBody */
        $requestBody = $builder->create(StreamInterface::class, [
            new WithReturn('__toString', [], $inputAsJson),
        ]);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['id', null], 'cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
            new WithReturn('getAttribute', ['contentType', null], 'application/json'),
            new WithReturn('getBody', [], $requestBody),
        ]);

        /** @var ModelInterface $model */
        $model = $builder->create(ModelInterface::class, []);

        /** @var DecoderInterface $decoder */
        $decoder = $builder->create(DecoderInterface::class, [
            new WithReturn('decode', [$inputAsJson, 'application/json'], $inputAsArray),
        ]);

        /** @var ObjectSchemaInterface $modelRequestSchema */
        $modelRequestSchema = $builder->create(ObjectSchemaInterface::class, [
            new WithException('parse', [$inputAsArray], $parserErrorException),
        ]);

        /** @var ParsingInterface $parsing */
        $parsing = $builder->create(ParsingInterface::class, [
            new WithReturn('getModelRequestSchema', [$request], $modelRequestSchema),
        ]);

        /** @var RepositoryInterface $repository */
        $repository = $builder->create(RepositoryInterface::class, [
            new WithReturn('findById', ['cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'], $model),
        ]);

        /** @var EncoderInterface $encoder */
        $encoder = $builder->create(EncoderInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $requestHandler = new UpdateRequestHandler(
            $decoder,
            $parsing,
            $repository,
            $encoder,
            $responseFactory
        );

        try {
            $requestHandler->handle($request);

            throw new \Exception('Expected Exception');
        } catch (HttpExceptionInterface $e) {
            self::assertSame([
                'type' => 'https://datatracker.ietf.org/doc/html/rfc4918#section-11.2',
                'status' => 422,
                'title' => 'Unprocessable Entity',
                'detail' => null,
                'instance' => null,
                'invalidParameters' => [],
            ], $e->jsonSerialize());
        }
    }

    public function testSuccessful(): void
    {
        $inputAsStdClass = new \stdClass();
        $inputAsStdClass->name = 'test';
        $inputAsArray = (array) $inputAsStdClass;
        $inputAsJson = json_encode($inputAsArray);

        $builder = new MockObjectBuilder();

        /** @var StreamInterface $requestBody */
        $requestBody = $builder->create(StreamInterface::class, [
            new WithReturn('__toString', [], $inputAsJson),
        ]);

        /** @var StreamInterface $responseBody */
        $responseBody = $builder->create(StreamInterface::class, [
            new WithReturn('write', [$inputAsJson], \strlen($inputAsJson)),
        ]);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['id', null], 'cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
            new WithReturn('getAttribute', ['contentType', null], 'application/json'),
            new WithReturn('getBody', [], $requestBody),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'application/json']),
            new WithReturn('getBody', [], $responseBody),
        ]);

        /** @var ModelInterface $model */
        $model = $builder->create(ModelInterface::class, []);

        /** @var DecoderInterface $decoder */
        $decoder = $builder->create(DecoderInterface::class, [
            new WithReturn('decode', [$inputAsJson, 'application/json'], $inputAsArray),
        ]);

        /** @var ModelRequestInterface $modelRequest */
        $modelRequest = $builder->create(ModelRequestInterface::class, [
            new WithReturn('updateModel', [$model], $model),
        ]);

        /** @var ObjectSchemaInterface $modelRequestSchema */
        $modelRequestSchema = $builder->create(ObjectSchemaInterface::class, [
            new WithReturn('parse', [$inputAsArray], $modelRequest),
        ]);

        /** @var ObjectSchemaInterface $modelResponseSchema */
        $modelResponseSchema = $builder->create(ObjectSchemaInterface::class, [
            new WithReturn('parse', [$model], $inputAsArray),
        ]);

        /** @var ParsingInterface $parsing */
        $parsing = $builder->create(ParsingInterface::class, [
            new WithReturn('getModelRequestSchema', [$request], $modelRequestSchema),
            new WithReturn('getModelResponseSchema', [$request], $modelResponseSchema),
        ]);

        /** @var RepositoryInterface $repository */
        $repository = $builder->create(RepositoryInterface::class, [
            new WithReturn('findById', ['cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'], $model),
            new WithReturnSelf('persist', [$model]),
            new WithReturnSelf('flush', []),
        ]);

        /** @var EncoderInterface $encoder */
        $encoder = $builder->create(EncoderInterface::class, [
            new WithReturn('encode', [$inputAsArray, 'application/json'], $inputAsJson),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [200, ''], $response),
        ]);

        $requestHandler = new UpdateRequestHandler(
            $decoder,
            $parsing,
            $repository,
            $encoder,
            $responseFactory
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
