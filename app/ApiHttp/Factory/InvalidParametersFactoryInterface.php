<?php

declare(strict_types=1);

namespace App\ApiHttp\Factory;

use Chubbyphp\Validation\Error\ErrorInterface;

interface InvalidParametersFactoryInterface
{
    /**
     * @param ErrorInterface[] $errors
     *
     * @return array
     */
    public function createInvalidParameters(array $errors): array;
}
