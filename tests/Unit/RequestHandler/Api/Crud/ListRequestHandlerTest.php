<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Api\Crud;

use App\Collection\CollectionInterface;
use App\Dto\Collection\CollectionRequestInterface;
use App\Parsing\ParsingInterface;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpExceptionInterface;
use Chubbyphp\Mock\MockMethod\WithException;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
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
 * @covers \App\RequestHandler\Api\Crud\ListRequestHandler
 *
 * @internal
 */
final class ListRequestHandlerTest extends TestCase
{
    public function testWithParsingError(): void
    {
        $parserErrorException = new ParserErrorException();

        $queryAsStdClass = new \stdClass();
        $queryAsStdClass->name = 'test';
        $queryAsArray = (array) $queryAsStdClass;

        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
            new WithReturn('getQueryParams', [], $queryAsArray),
        ]);

        $collectionRequestSchema = $builder->create(ObjectSchemaInterface::class, [
            new WithException('parse', [$queryAsArray], $parserErrorException),
        ]);

        /** @var ParsingInterface $parsing */
        $parsing = $builder->create(ParsingInterface::class, [
            new WithReturn('getCollectionRequestSchema', [$request], $collectionRequestSchema),
        ]);

        /** @var RepositoryInterface $repository */
        $repository = $builder->create(RepositoryInterface::class, []);

        /** @var EncoderInterface $encoder */
        $encoder = $builder->create(EncoderInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $requestHandler = new ListRequestHandler(
            $parsing,
            $repository,
            $encoder,
            $responseFactory
        );

        try {
            $requestHandler->handle($request);
            self::fail('Expected Exception');
        } catch (HttpExceptionInterface $e) {
            self::assertSame([
                'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.1',
                'status' => 400,
                'title' => 'Bad Request',
                'detail' => null,
                'instance' => null,
                'invalidParameters' => [],
            ], $e->jsonSerialize());
        }
    }

    public function testSuccessful(): void
    {
        $queryAsStdClass = new \stdClass();
        $queryAsStdClass->name = 'test';
        $queryAsArray = (array) $queryAsStdClass;
        $queryAsJson = json_encode($queryAsArray);

        $builder = new MockObjectBuilder();

        /** @var StreamInterface $responseBody */
        $responseBody = $builder->create(StreamInterface::class, [
            new WithReturn('write', [$queryAsJson], \strlen($queryAsJson)),
        ]);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
            new WithReturn('getQueryParams', [], $queryAsArray),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'application/json']),
            new WithReturn('getBody', [], $responseBody),
        ]);

        /** @var CollectionInterface $collection */
        $collection = $builder->create(CollectionInterface::class, []);

        /** @var CollectionRequestInterface $collectionRequest */
        $collectionRequest = $builder->create(CollectionRequestInterface::class, [
            new WithReturn('createCollection', [], $collection),
        ]);

        /** @var ObjectSchemaInterface $collectionRequestSchema */
        $collectionRequestSchema = $builder->create(ObjectSchemaInterface::class, [
            new WithReturn('parse', [$queryAsArray], $collectionRequest),
        ]);

        /** @var ObjectSchemaInterface $collectionResponseSchema */
        $collectionResponseSchema = $builder->create(ObjectSchemaInterface::class, [
            new WithReturn('parse', [$collection], $queryAsArray),
        ]);

        /** @var ParsingInterface $parsing */
        $parsing = $builder->create(ParsingInterface::class, [
            new WithReturn('getCollectionRequestSchema', [$request], $collectionRequestSchema),
            new WithReturn('getCollectionResponseSchema', [$request], $collectionResponseSchema),
        ]);

        /** @var RepositoryInterface $repository */
        $repository = $builder->create(RepositoryInterface::class, [
            new WithoutReturn('resolveCollection', [$collection]),
        ]);

        /** @var EncoderInterface $encoder */
        $encoder = $builder->create(EncoderInterface::class, [
            new WithReturn('encode', [$queryAsArray, 'application/json'], $queryAsJson),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [200, ''], $response),
        ]);

        $requestHandler = new ListRequestHandler(
            $parsing,
            $repository,
            $encoder,
            $responseFactory
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
