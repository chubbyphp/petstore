<?php

declare(strict_types=1);

namespace App\Tests\Helper;

final class AssertHelper
{
    public static function readProperty(string $property, object $object): mixed
    {
        return (new \ReflectionProperty($object, $property))->getValue($object);
    }
}
