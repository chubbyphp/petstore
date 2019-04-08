<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Crud;

use App\Controller\Crud\DeleteController;
use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\Controller\Crud\DeleteController
 */
class DeleteControllerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateResourceNotFound(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn(null),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createResourceNotFound')
                ->with(['model' => 'cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'], 'application/json', null)
                ->willReturn($response),
        ]);

        $controller = new DeleteController($repository, $responseManager);

        self::assertSame($response, $controller->handle($request));
    }

    public function testSuccessful(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn($model),
            Call::create('remove')->with($model),
            Call::create('flush')->with(),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createEmpty')
                ->with('application/json', 204)
                ->willReturn($response),
        ]);

        $controller = new DeleteController($repository, $responseManager);

        self::assertSame($response, $controller->handle($request));
    }
}
