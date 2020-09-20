<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Assert;

trait AssertTrait
{
    // @todo: remove when phpunit min version >= 9
    public static function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        if (!is_callable([Assert::class, 'assertMatchesRegularExpression'])) {
            Assert::assertRegExp($pattern, $string, $message);

            return;
        }

        Assert::assertMatchesRegularExpression($pattern, $string, $message);
    }

    // @todo: remove when phpunit min version >= 9
    public static function assertDirectoryDoesNotExist(string $directory, string $message = ''): void
    {
        if (!is_callable([Assert::class, 'assertDirectoryDoesNotExist'])) {
            Assert::assertDirectoryNotExists($directory, $message);

            return;
        }

        Assert::assertDirectoryDoesNotExist($directory, $message);
    }
}
