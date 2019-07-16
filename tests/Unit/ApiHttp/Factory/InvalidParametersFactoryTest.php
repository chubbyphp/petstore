<?php

declare(strict_types=1);

namespace App\Tests\Unit\ApiHttp\Factory;

use App\ApiHttp\Factory\InvalidParametersFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Validation\Error\ErrorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ApiHttp\Factory\InvalidParametersFactory
 *
 * @internal
 */
class InvalidParametersFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateResponse(): void
    {
        /** @var ErrorInterface|MockObject $error */
        $error = $this->getMockByCalls(ErrorInterface::class, [
            Call::create('getPath')->with()->willReturn('path.to.property'),
            Call::create('getKey')->with()->willReturn('constraint.numericrange.outofrange'),
            Call::create('getArguments')
                ->with()
                ->willReturn(['value' => 5, 'min' => 2, 'max' => 4]),
        ]);

        $factory = new InvalidParametersFactory();

        $invalidParameters = $factory->createInvalidParameters([$error]);

        self::assertSame([
            [
                'name' => 'path.to.property',
                'reason' => 'constraint.numericrange.outofrange',
                'details' => [
                    'value' => 5,
                    'min' => 2,
                    'max' => 4,
                ],
            ],
        ], $invalidParameters);
    }
}
