<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\InvocationStrategyFactory;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;

/**
 * @covers \App\Core\ServiceFactory\Framework\InvocationStrategyFactory
 *
 * @internal
 */
final class InvocationStrategyFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        $attributes = ['key1' => 'value1'];

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            ...array_map(
                fn (string $key, mixed $value) => new WithReturnSelf('withAttribute', [$key, $value]),
                array_keys($attributes),
                array_values($attributes)
            ),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        $factory = new InvocationStrategyFactory();

        $service = $factory();

        $service(
            fn (ServerRequestInterface $_): ResponseInterface => $response,
            $request,
            $response,
            $attributes
        );

        self::assertInstanceOf(InvocationStrategyInterface::class, $service);
    }
}
