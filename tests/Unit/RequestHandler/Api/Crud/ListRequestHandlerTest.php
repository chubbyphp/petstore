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
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testWithParsingError(): void
    {
        $parserErrorException = new ParserErrorException();

        $queryAsStdClass = new \stdClass();
        $queryAsStdClass->name = 'test';
        $queryAsArray = (array) $queryAsStdClass;

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getQueryParams')->with()->willReturn($queryAsArray),
        ]);

        /** @var MockObject|ObjectSchemaInterface $collectionRequestSchema */
        $collectionRequestSchema = $this->getMockByCalls(ObjectSchemaInterface::class, [
            Call::create('parse')->with($queryAsArray)->willThrowException($parserErrorException),
        ]);

        /** @var MockObject|ParsingInterface $parsing */
        $parsing = $this->getMockByCalls(ParsingInterface::class, [
            Call::create('getCollectionRequestSchema')->with($request)->willReturn($collectionRequestSchema),
        ]);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $requestHandler = new ListRequestHandler(
            $parsing,
            $repository,
            $encoder,
            $responseFactory
        );

        try {
            $requestHandler->handle($request);
            self::fail('Expected Exception');
        } catch (\Throwable $e) {
            self::assertInstanceOf(HttpExceptionInterface::class, $e);
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

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($queryAsJson),
        ]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getQueryParams')->with()->willReturn($queryAsArray),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var CollectionInterface|MockObject $collection */
        $collection = $this->getMockByCalls(CollectionInterface::class);

        /** @var CollectionRequestInterface|MockObject $collectionRequest */
        $collectionRequest = $this->getMockByCalls(CollectionRequestInterface::class, [
            Call::create('createCollection')->with()->willReturn($collection),
        ]);

        /** @var MockObject|ObjectSchemaInterface $collectionRequestSchema */
        $collectionRequestSchema = $this->getMockByCalls(ObjectSchemaInterface::class, [
            Call::create('parse')->with($queryAsArray)->willReturn($collectionRequest),
        ]);

        /** @var MockObject|ObjectSchemaInterface $collectionResponseSchema */
        $collectionResponseSchema = $this->getMockByCalls(ObjectSchemaInterface::class, [
            Call::create('parse')->with($collection)->willReturn($queryAsArray),
        ]);

        /** @var MockObject|ParsingInterface $parsing */
        $parsing = $this->getMockByCalls(ParsingInterface::class, [
            Call::create('getCollectionRequestSchema')->with($request)->willReturn($collectionRequestSchema),
            Call::create('getCollectionResponseSchema')->with($request)->willReturn($collectionResponseSchema),
        ]);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('resolveCollection')->with($collection),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class, [
            Call::create('encode')->with($queryAsArray, 'application/json')->willReturn($queryAsJson),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
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
