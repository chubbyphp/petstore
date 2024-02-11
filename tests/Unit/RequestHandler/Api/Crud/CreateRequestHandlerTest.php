<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Api\Crud;

use App\Dto\Model\ModelRequestInterface;
use App\Model\ModelInterface;
use App\Parsing\ParsingInterface;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
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
 * @covers \App\RequestHandler\Api\Crud\CreateRequestHandler
 *
 * @internal
 */
final class CreateRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithParsingError(): void
    {
        $parserErrorException = new ParserErrorException();

        $inputAsStdClass = new \stdClass();
        $inputAsStdClass->name = 'test';
        $inputAsArray = (array) $inputAsStdClass;
        $inputAsJson = json_encode($inputAsArray);

        /** @var MockObject|StreamInterface $requestBody */
        $requestBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('__toString')->with()->willReturn($inputAsJson),
        ]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
            Call::create('getBody')->with()->willReturn($requestBody),
        ]);

        /** @var DecoderInterface|MockObject $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class, [
            Call::create('decode')->with($inputAsJson, 'application/json')->willReturn($inputAsArray),
        ]);

        /** @var MockObject|ObjectSchemaInterface $modelRequestSchema */
        $modelRequestSchema = $this->getMockByCalls(ObjectSchemaInterface::class, [
            Call::create('parse')->with($inputAsArray)->willThrowException($parserErrorException),
        ]);

        /** @var MockObject|ParsingInterface $parsing */
        $parsing = $this->getMockByCalls(ParsingInterface::class, [
            Call::create('getModelRequestSchema')->with($request)->willReturn($modelRequestSchema),
        ]);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $requestHandler = new CreateRequestHandler(
            $decoder,
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

        /** @var MockObject|StreamInterface $requestBody */
        $requestBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('__toString')->with()->willReturn($inputAsJson),
        ]);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($inputAsJson),
        ]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
            Call::create('getBody')->with()->willReturn($requestBody),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var MockObject|ModelInterface $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        /** @var DecoderInterface|MockObject $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class, [
            Call::create('decode')->with($inputAsJson, 'application/json')->willReturn($inputAsArray),
        ]);

        /** @var MockObject|ModelRequestInterface $modelRequest */
        $modelRequest = $this->getMockByCalls(ModelRequestInterface::class, [
            Call::create('createModel')->with()->willReturn($model),
        ]);

        /** @var MockObject|ObjectSchemaInterface $modelRequestSchema */
        $modelRequestSchema = $this->getMockByCalls(ObjectSchemaInterface::class, [
            Call::create('parse')->with($inputAsArray)->willReturn($modelRequest),
        ]);

        /** @var MockObject|ObjectSchemaInterface $modelResponseSchema */
        $modelResponseSchema = $this->getMockByCalls(ObjectSchemaInterface::class, [
            Call::create('parse')->with($model)->willReturn($inputAsArray),
        ]);

        /** @var MockObject|ParsingInterface $parsing */
        $parsing = $this->getMockByCalls(ParsingInterface::class, [
            Call::create('getModelRequestSchema')->with($request)->willReturn($modelRequestSchema),
            Call::create('getModelResponseSchema')->with($request)->willReturn($modelResponseSchema),
        ]);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('persist')->with($model),
            Call::create('flush')->with(),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class, [
            Call::create('encode')->with($inputAsArray, 'application/json')->willReturn($inputAsJson),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(201, '')->willReturn($response),
        ]);

        $requestHandler = new CreateRequestHandler(
            $decoder,
            $parsing,
            $repository,
            $encoder,
            $responseFactory
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
