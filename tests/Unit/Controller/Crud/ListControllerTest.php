<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Crud;

use App\ApiHttp\Factory\ErrorFactoryInterface;
use App\Collection\CollectionInterface;
use App\Controller\Crud\ListController;
use App\Factory\Collection\FactoryInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Validation\Error\ErrorInterface as ValidationErrorInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\Controller\Crud\ListController
 */
class ListControllerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateWithValidationError()
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
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
                ->with(ErrorInterface::SCOPE_QUERY, [$validationError])
                ->willReturn($error),
        ]);

        /** @var CollectionInterface|MockObject $collection */
        $collection = $this->getMockByCalls(CollectionInterface::class);

        /** @var FactoryInterface|MockObject $factory */
        $factory = $this->getMockByCalls(FactoryInterface::class, [
            Call::create('create')->with()->willReturn($collection),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestQuery')
                ->with($request, $collection, null)
                ->willReturn($collection),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createFromError')
                ->with($error, 'application/json', 400, null)
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($collection, null, '')->willReturn([$validationError]),
        ]);

        $controller = new ListController(
            $errorFactory,
            $factory,
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
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ErrorFactoryInterface|MockObject $errorFactory */
        $errorFactory = $this->getMockByCalls(ErrorFactoryInterface::class);

        /** @var CollectionInterface|MockObject $collection */
        $collection = $this->getMockByCalls(CollectionInterface::class);

        /** @var FactoryInterface|MockObject $factory */
        $factory = $this->getMockByCalls(FactoryInterface::class, [
            Call::create('create')->with()->willReturn($collection),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('resolveCollection')->with($collection),
        ]);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestQuery')
                ->with($request, $collection, null)
                ->willReturn($collection),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('create')
                ->with($collection, 'application/json', 200, null)
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($collection, null, '')->willReturn([]),
        ]);

        $controller = new ListController(
            $errorFactory,
            $factory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $controller($request));
    }
}
