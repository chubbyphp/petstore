<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Crud;

use App\ApiHttp\Factory\ErrorFactoryInterface;
use App\Controller\Crud\UpdateController;
use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Argument\ArgumentInstanceOf;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Chubbyphp\Validation\Error\ErrorInterface as ValidationErrorInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\Controller\Crud\UpdateController
 */
class UpdateControllerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateResourceNotFound()
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ValidationErrorInterface|MockObject $validationError */
        $validationError = $this->getMockByCalls(ValidationErrorInterface::class);

        /** @var ErrorFactoryInterface|MockObject $errorFactory */
        $errorFactory = $this->getMockByCalls(ErrorFactoryInterface::class);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn(null),
        ]);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createResourceNotFound')
                ->with(['model' => 'cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'], 'application/json', null)
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class);

        $controller = new UpdateController(
            $errorFactory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $controller($request));
    }

    public function testCreateWithValidationError()
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ValidationErrorInterface|MockObject $validationError */
        $validationError = $this->getMockByCalls(ValidationErrorInterface::class);

        /** @var ErrorInterface|MockObject $error */
        $error = $this->getMockByCalls(ErrorInterface::class);

        /** @var ErrorFactoryInterface|MockObject $errorFactory */
        $errorFactory = $this->getMockByCalls(ErrorFactoryInterface::class, [
            Call::create('createFromValidationError')
                ->with(ErrorInterface::SCOPE_BODY, [$validationError])
                ->willReturn($error),
        ]);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class);

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
                        self::assertTrue($context->isResetMissingFields());
                    })
                )
                ->willReturn($model),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createFromError')
                ->with($error, 'application/json', 422, null)
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($model, null, '')->willReturn([$validationError]),
        ]);

        $controller = new UpdateController(
            $errorFactory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $controller($request));
    }

    public function testSuccessful()
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ErrorFactoryInterface|MockObject $errorFactory */
        $errorFactory = $this->getMockByCalls(ErrorFactoryInterface::class);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class, [
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
                        self::assertTrue($context->isResetMissingFields());
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

        $controller = new UpdateController(
            $errorFactory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $controller($request));
    }
}
