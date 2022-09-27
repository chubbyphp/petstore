<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Api\Crud;

use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\HttpException\HttpExceptionInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Argument\ArgumentInstanceOf;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Chubbyphp\Validation\Error\ErrorInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\RequestHandler\Api\Crud\UpdateRequestHandler
 *
 * @internal
 */
final class UpdateRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateResourceNotFoundInvalidUuid(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('1234'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var MockObject|RequestManagerInterface $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class);

        /** @var MockObject|ResponseManagerInterface $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var MockObject|ValidatorInterface $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class);

        $requestHandler = new UpdateRequestHandler(
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        try {
            $requestHandler->handle($request);
            self::fail('Expected Exception');
        } catch (\Throwable $e) {
            self::assertInstanceOf(HttpExceptionInterface::class, $e);
            self::assertSame(404, $e->getStatus());
        }
    }

    public function testCreateResourceNotFoundMissingModel(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn(null),
        ]);

        /** @var MockObject|RequestManagerInterface $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class);

        /** @var MockObject|ResponseManagerInterface $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var MockObject|ValidatorInterface $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class);

        $requestHandler = new UpdateRequestHandler(
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        try {
            $requestHandler->handle($request);
            self::fail('Expected Exception');
        } catch (\Throwable $e) {
            self::assertInstanceOf(HttpExceptionInterface::class, $e);
            self::assertSame(404, $e->getStatus());
        }
    }

    public function testCreateWithValidationError(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ErrorInterface|MockObject $error */
        $error = $this->getMockByCalls(ErrorInterface::class, [
            Call::create('getPath')->with()->willReturn('name'),
            Call::create('getKey')->with()->willReturn('notunique'),
            Call::create('getArguments')->with()->willReturn([]),
        ]);

        /** @var MockObject|ModelInterface $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn($model),
        ]);

        /** @var MockObject|RequestManagerInterface $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestBody')
                ->with(
                    $request,
                    $model,
                    'application/json',
                    new ArgumentCallback(static function (DenormalizerContextInterface $context): void {
                        self::assertTrue($context->isClearMissing());
                        self::assertSame(
                            ['id', 'createdAt', 'updatedAt', '_links'],
                            $context->getAllowedAdditionalFields()
                        );
                    })
                )
                ->willReturn($model),
        ]);

        /** @var MockObject|ResponseManagerInterface $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var MockObject|ValidatorInterface $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($model, null, '')->willReturn([$error]),
        ]);

        $requestHandler = new UpdateRequestHandler(
            $repository,
            $requestManager,
            $responseManager,
            $validator
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
                'invalidParameters' => [
                    0 => [
                        'name' => 'name',
                        'reason' => 'notunique',
                        'details' => [],
                    ],
                ],
            ], $e->jsonSerialize());
        }
    }

    public function testSuccessful(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|ModelInterface $model */
        $model = $this->getMockByCalls(ModelInterface::class, [
            Call::create('setUpdatedAt')->with(new ArgumentInstanceOf(\DateTimeImmutable::class)),
        ]);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn($model),
            Call::create('persist')->with($model),
            Call::create('flush')->with(),
        ]);

        /** @var MockObject|RequestManagerInterface $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestBody')
                ->with(
                    $request,
                    $model,
                    'application/json',
                    new ArgumentCallback(static function (DenormalizerContextInterface $context): void {
                        self::assertTrue($context->isClearMissing());
                        self::assertSame(
                            ['id', 'createdAt', 'updatedAt', '_links'],
                            $context->getAllowedAdditionalFields()
                        );
                    })
                )
                ->willReturn($model),
        ]);

        /** @var MockObject|ResponseManagerInterface $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('create')
                ->with(
                    $model,
                    'application/json',
                    200,
                    new ArgumentCallback(static function (NormalizerContextInterface $context) use ($request): void {
                        self::assertSame($request, $context->getRequest());
                    })
                )
                ->willReturn($response),
        ]);

        /** @var MockObject|ValidatorInterface $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($model, null, '')->willReturn([]),
        ]);

        $requestHandler = new UpdateRequestHandler(
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
