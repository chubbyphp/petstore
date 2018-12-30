<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory\Model;

use App\Factory\Model\PetFactory;
use App\Model\ModelInterface;
use App\Model\Pet;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Factory\Model\PetFactory
 */
final class PetFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreate(): void
    {
        $factory = new PetFactory();

        self::assertInstanceOf(Pet::class, $factory->create());
    }

    public function testResetWithInvalidModel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/^Model of class "[^"]+" given, "App\\\Model\\\Pet" required$/');

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        $factory = new PetFactory();
        $factory->reset($model);
    }

    public function testReset(): void
    {
        $model = new Pet();
        $model->setName('name');
        $model->setTag('tag');

        $factory = new PetFactory();
        $factory->reset($model);

        $reflectionName = new \ReflectionProperty($model, 'name');
        $reflectionName->setAccessible(true);

        self::assertNull($reflectionName->getValue($model));

        $reflectionTag = new \ReflectionProperty($model, 'tag');
        $reflectionTag->setAccessible(true);

        self::assertNull($reflectionTag->getValue($model));
    }

    public function testGetClass(): void
    {
        $factory = new PetFactory();

        self::assertSame(Pet::class, $factory->getClass());
    }
}
