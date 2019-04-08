<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Crud;

use App\ApiHttp\Factory\ErrorFactoryInterface;
use App\Controller\Crud\CreateController;
use App\Factory\ModelFactoryInterface;
use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Chubbyphp\Validation\Error\ErrorInterface as ValidationErrorInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\Controller\Crud\CreateController
 */
class CreateControllerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateWithValidationError(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
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

        /** @var ModelFactoryInterface|MockObject $factory */
        $factory = $this->getMockByCalls(ModelFactoryInterface::class, [
            Call::create('create')->with()->willReturn($model),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestBody')
                ->with($request, $model, 'application/json', null)
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

        $controller = new CreateController(
            $errorFactory,
            $factory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $controller->handle($request));
    }

    public function testSuccessful(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ErrorFactoryInterface|MockObject $errorFactory */
        $errorFactory = $this->getMockByCalls(ErrorFactoryInterface::class);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        /** @var ModelFactoryInterface|MockObject $factory */
        $factory = $this->getMockByCalls(ModelFactoryInterface::class, [
            Call::create('create')->with()->willReturn($model),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('persist')->with($model),
            Call::create('flush')->with(),
        ]);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestBody')
                ->with($request, $model, 'application/json', null)
                ->willReturn($model),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('create')
                ->with(
                    $model,
                    'application/json',
                    201,
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

        $controller = new CreateController(
            $errorFactory,
            $factory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $controller->handle($request));
    }
}
