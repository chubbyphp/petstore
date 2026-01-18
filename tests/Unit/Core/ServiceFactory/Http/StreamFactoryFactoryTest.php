<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Http;

use App\Core\ServiceFactory\Http\StreamFactoryFactory;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\StreamFactory;

/**
 * @covers \App\Core\ServiceFactory\Http\StreamFactoryFactory
 *
 * @internal
 */
final class StreamFactoryFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $factory = new StreamFactoryFactory();

        self::assertInstanceOf(StreamFactory::class, $factory());
    }
}
