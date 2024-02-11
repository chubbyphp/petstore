<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Api\Crud;

use App\Model\ModelInterface;
use App\Parsing\ParsingInterface;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpExceptionInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Parsing\Schema\ObjectSchemaInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \App\RequestHandler\Api\Crud\ReadRequestHandler
 *
 * @internal
 */
final class ReadRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testResourceNotFoundInvalidUuid(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('1234'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ParsingInterface $parsing */
        $parsing = $this->getMockByCalls(ParsingInterface::class);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $requestHandler = new ReadRequestHandler(
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
            self::assertSame(404, $e->getStatus());
        }
    }

    public function testResourceNotFoundMissingModel(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ParsingInterface $parsing */
        $parsing = $this->getMockByCalls(ParsingInterface::class);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn(null),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $requestHandler = new ReadRequestHandler(
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
            self::assertSame(404, $e->getStatus());
        }
    }

    public function testSuccessful(): void
    {
        $inputAsStdClass = new \stdClass();
        $inputAsStdClass->name = 'test';
        $inputAsArray = (array) $inputAsStdClass;
        $inputAsJson = json_encode($inputAsArray);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($inputAsJson),
        ]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var MockObject|ModelInterface $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        /** @var MockObject|ObjectSchemaInterface $modelResponseSchema */
        $modelResponseSchema = $this->getMockByCalls(ObjectSchemaInterface::class, [
            Call::create('parse')->with($model)->willReturn($inputAsArray),
        ]);

        /** @var MockObject|ParsingInterface $parsing */
        $parsing = $this->getMockByCalls(ParsingInterface::class, [
            Call::create('getModelResponseSchema')->with($request)->willReturn($modelResponseSchema),
        ]);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn($model),
        ]);

        /** @var EncoderInterface|MockObject $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class, [
            Call::create('encode')->with($inputAsArray, 'application/json')->willReturn($inputAsJson),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
        ]);

        $requestHandler = new ReadRequestHandler(
            $parsing,
            $repository,
            $encoder,
            $responseFactory
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
