<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Validation\Constraint;

use App\Mapping\Validation\Constraint\SortConstraint;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Validation\ValidatorContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Chubbyphp\Validation\Error\Error;

/**
 * @covers \App\Mapping\Validation\Constraint\SortConstraint
 */
final class SortConstraintTest extends TestCase
{
    use MockByCallsTrait;

    public function testValidateWithString()
    {
        /** @var ValidatorContextInterface|MockObject $context */
        $context = $this->getMockByCalls(ValidatorContextInterface::class);

        $constraint = new SortConstraint(['name']);

        self::assertEquals(
            [new Error('path', 'constraint.sort.invalidtype', ['type' => 'string'])],
            $constraint->validate('path', 'name', $context)
        );
    }

    public function testValidateWithStdClass()
    {
        /** @var ValidatorContextInterface|MockObject $context */
        $context = $this->getMockByCalls(ValidatorContextInterface::class);

        $constraint = new SortConstraint(['name']);

        self::assertEquals(
            [new Error('path', 'constraint.sort.invalidtype', ['type' => \stdClass::class])],
            $constraint->validate('path', new \stdClass(), $context)
        );
    }

    public function testValidateWithUnsupportedFieldAndUnsupportedOrder()
    {
        /** @var ValidatorContextInterface|MockObject $context */
        $context = $this->getMockByCalls(ValidatorContextInterface::class);

        $constraint = new SortConstraint(['name']);

        self::assertEquals(
            [
                new Error(
                    'path',
                    'constraint.sort.field.notallowed',
                    ['field' => 'unknown', 'allowedFields' => ['name']]
                ),
                new Error(
                    'path',
                    'constraint.sort.order.notallowed',
                    ['field' => 'unknown', 'order' => 'test', 'allowedOrders' => ['asc', 'desc']]
                ),
            ],
            $constraint->validate('path', ['name' => 'asc', 'unknown' => 'test'], $context)
        );
    }

    public function testValidateWithUnsupportedFieldAndUnsupportedOrderType()
    {
        /** @var ValidatorContextInterface|MockObject $context */
        $context = $this->getMockByCalls(ValidatorContextInterface::class);

        $constraint = new SortConstraint(['name']);

        self::assertEquals(
            [
                new Error(
                    'path',
                    'constraint.sort.field.notallowed',
                    ['field' => 'unknown', 'allowedFields' => ['name']]
                ),
                new Error(
                    'path',
                    'constraint.sort.order.invalidtype',
                    ['field' => 'unknown', 'type' => \stdClass::class]
                ),
            ],
            $constraint->validate('path', ['name' => 'asc', 'unknown' => new \stdClass()], $context)
        );
    }

    public function testValidate()
    {
        /** @var ValidatorContextInterface|MockObject $context */
        $context = $this->getMockByCalls(ValidatorContextInterface::class);

        $constraint = new SortConstraint(['name']);

        self::assertSame([], $constraint->validate('path', ['name' => 'asc'], $context));
    }
}
