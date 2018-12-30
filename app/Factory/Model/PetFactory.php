<?php

declare(strict_types=1);

namespace App\Factory\Model;

use App\Factory\ModelFactoryInterface;
use App\Model\ModelInterface;
use App\Model\Pet;

final class PetFactory implements ModelFactoryInterface
{
    /**
     * @return ModelInterface
     */
    public function create(): ModelInterface
    {
        return new Pet();
    }

    /**
     * @param ModelInterface $model
     */
    public function reset(ModelInterface $model): void
    {
        if (!$model instanceof Pet) {
            throw new \InvalidArgumentException(
                sprintf('Model of class "%s" given, "%s" required', get_class($model), Pet::class)
            );
        }

        $newModel = $this->create();

        foreach (['name', 'tag'] as $property) {
            $reflectionProperty = new \ReflectionProperty(Pet::class, $property);
            $reflectionProperty->setAccessible(true);

            $reflectionProperty->setValue($model, $reflectionProperty->getValue($newModel));
        }
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return Pet::class;
    }
}
