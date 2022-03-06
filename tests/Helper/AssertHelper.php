<?php

declare(strict_types=1);

namespace App\Tests\Helper;

final class AssertHelper
{
    public static function readProperty(string $property, object $object): mixed
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}
