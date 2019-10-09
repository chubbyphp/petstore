<?php

declare(strict_types=1);

namespace App\ApiHttp\Factory;

use Chubbyphp\Validation\Error\ErrorInterface;

interface InvalidParametersFactoryInterface
{
    /**
     * @param array<ErrorInterface> $errors
     *
     * @return array<array<string, array|string>>
     */
    public function createInvalidParameters(array $errors): array;
}
