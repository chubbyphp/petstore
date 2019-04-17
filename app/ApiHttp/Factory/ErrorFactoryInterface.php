<?php

declare(strict_types=1);

namespace App\ApiHttp\Factory;

use Chubbyphp\Validation\Error\ErrorInterface as ValidationErrorInterface;

interface ErrorFactoryInterface
{
    /**
     * @param ValidationErrorInterface[] $errors
     *
     * @return array
     */
    public function createErrorMessages(array $errors): array;
}
