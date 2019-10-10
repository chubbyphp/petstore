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
        /** @var ErrorInterface|MockObject $error1 */
        $error1 = $this->getMockByCalls(ErrorInterface::class, [
            Call::create('getPath')->with()->willReturn('path.to.property1'),
            Call::create('getKey')->with()->willReturn('constraint.numericrange.outofrange'),
            Call::create('getArguments')
                ->with()
                ->willReturn(['value' => 5, 'min' => 2, 'max' => 4]),
        ]);

        /** @var ErrorInterface|MockObject $error2 */
        $error2 = $this->getMockByCalls(ErrorInterface::class, [
            Call::create('getPath')->with()->willReturn('path.to.property2'),
            Call::create('getKey')->with()->willReturn('constraint.type.invalidtype'),
            Call::create('getArguments')
                ->with()
                ->willReturn(['type' => 'string', 'wishedType' => 'int']),
        ]);

        $factory = new InvalidParametersFactory();

        $invalidParameters = $factory->createInvalidParameters([$error1, $error2]);

        self::assertSame([
            [
                'name' => 'path.to.property1',
                'reason' => 'constraint.numericrange.outofrange',
                'details' => ['value' => 5, 'min' => 2, 'max' => 4],
            ],
            [
                'name' => 'path.to.property2',
                'reason' => 'constraint.type.invalidtype',
                'details' => ['type' => 'string', 'wishedType' => 'int'],
            ],
        ], $invalidParameters);
    }
}
