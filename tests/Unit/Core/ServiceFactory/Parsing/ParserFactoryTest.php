<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Parsing;

use App\Core\ServiceFactory\Parsing\ParserFactory;
use Chubbyphp\Parsing\ParserInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Core\ServiceFactory\Parsing\ParserFactory
 *
 * @internal
 */
final class ParserFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $factory = new ParserFactory();

        self::assertInstanceOf(ParserInterface::class, $factory());
    }
}
