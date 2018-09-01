<?php

declare(strict_types=1);

namespace App\Tests\Unit\ApiHttp\Factory;

use App\ApiHttp\Factory\ErrorFactory;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Validation\Error\ErrorInterface as ValidationErrorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ApiHttp\Factory\ErrorFactory
 */
class ErrorFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateResponse()
    {
        /** @var ValidationErrorInterface|MockObject $validationError */
        $validationError = $this->getMockByCalls(ValidationErrorInterface::class, [
            Call::create('getPath')->with()->willReturn('path.to.property'),
            Call::create('getKey')->with()->willReturn('constraint.numericrange.outofrange'),
            Call::create('getArguments')
                ->with()
                ->willReturn(['value' => 5, 'min' => 2, 'max' => 4]),
        ]);

        $factory = new ErrorFactory();

        $error = $factory->createFromValidationError(ErrorInterface::SCOPE_BODY, [$validationError]);

        self::assertInstanceOf(ErrorInterface::class, $error);

        self::assertSame(ErrorInterface::SCOPE_BODY, $error->getScope());
        self::assertSame('validation', $error->getKey());
        self::assertSame('there are validation errors', $error->getDetail());
        self::assertNull($error->getReference());

        self::assertSame([
            'path' => [
                'to' => [
                    'property' => [
                        [
                            'key' => 'constraint.numericrange.outofrange',
                            'arguments' => [
                                'value' => 5,
                                'min' => 2,
                                'max' => 4,
                            ],
                        ],
                    ],
                ],
            ],
          ], $error->getArguments());
    }
}
