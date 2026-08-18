<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Http;

use App\Core\ServiceFactory\Http\RequestFactoryFactory;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;

/**
 * @covers \App\Core\ServiceFactory\Http\RequestFactoryFactory
 *
 * @internal
 */
final class RequestFactoryFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $factory = new RequestFactoryFactory();

        self::assertInstanceOf(RequestFactory::class, $factory());
    }
}
