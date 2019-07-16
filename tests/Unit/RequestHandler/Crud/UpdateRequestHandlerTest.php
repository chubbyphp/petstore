<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Crud;

use App\ApiHttp\Factory\InvalidParametersFactoryInterface;
use App\RequestHandler\Crud\UpdateRequestHandler;
use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotFound;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Argument\ArgumentInstanceOf;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnprocessableEntity;

/**
 * @covers \App\RequestHandler\Crud\UpdateRequestHandler
 */
class UpdateRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateResourceNotFound(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var InvalidParametersFactoryInterface|MockObject $invalidParametersFactory */
        $invalidParametersFactory = $this->getMockByCalls(InvalidParametersFactoryInterface::class);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn(null),
        ]);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createFromApiProblem')
                ->with(
                    new ArgumentCallback(function (NotFound $apiProblem) {}),
                    'application/json',
                    null
                )
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class);

        $requestHandler = new UpdateRequestHandler(
            $invalidParametersFactory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testCreateWithValidationError(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ErrorInterface|MockObject $error */
        $error = $this->getMockByCalls(ErrorInterface::class);

        $invalidParameters = [
            ['name' => 'name', 'reason' => 'notunique', 'details' => []],
        ];

        /** @var InvalidParametersFactoryInterface|MockObject $invalidParametersFactory */
        $invalidParametersFactory = $this->getMockByCalls(InvalidParametersFactoryInterface::class, [
            Call::create('createInvalidParameters')
                ->with([$error])
                ->willReturn($invalidParameters),
        ]);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class, [
            Call::create('reset')->with(),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn($model),
        ]);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestBody')
                ->with(
                    $request,
                    $model,
                    'application/json',
                    new ArgumentCallback(function (DenormalizerContextInterface $context) {
                        self::assertSame(
                            ['id', 'createdAt', 'updatedAt', '_links'],
                            $context->getAllowedAdditionalFields()
                        );
                    })
                )
                ->willReturn($model),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createFromApiProblem')
                ->with(
                    new ArgumentCallback(function (UnprocessableEntity $apiProblem) use ($invalidParameters) {
                        self::assertSame($invalidParameters, $apiProblem->getInvalidParameters());
                    }),
                    'application/json',
                    null
                )
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($model, null, '')->willReturn([$error]),
        ]);

        $requestHandler = new UpdateRequestHandler(
            $invalidParametersFactory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testSuccessful(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var InvalidParametersFactoryInterface|MockObject $invalidParametersFactory */
        $invalidParametersFactory = $this->getMockByCalls(InvalidParametersFactoryInterface::class);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class, [
            Call::create('reset')->with(),
            Call::create('setUpdatedAt')->with(new ArgumentInstanceOf(\DateTime::class)),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn($model),
            Call::create('persist')->with($model),
            Call::create('flush')->with(),
        ]);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestBody')
                ->with(
                    $request,
                    $model,
                    'application/json',
                    new ArgumentCallback(function (DenormalizerContextInterface $context) {
                        self::assertSame(
                            ['id', 'createdAt', 'updatedAt', '_links'],
                            $context->getAllowedAdditionalFields()
                        );
                    })
                )
                ->willReturn($model),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('create')
                ->with(
                    $model,
                    'application/json',
                    200,
                    new ArgumentCallback(function (NormalizerContextInterface $context) use ($request) {
                        self::assertSame($request, $context->getRequest());
                    })
                )
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($model, null, '')->willReturn([]),
        ]);

        $requestHandler = new UpdateRequestHandler(
            $invalidParametersFactory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
