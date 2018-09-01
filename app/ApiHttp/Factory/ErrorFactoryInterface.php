<?php

declare(strict_types=1);

namespace App\ApiHttp\Factory;

use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\Validation\Error\ErrorInterface as ValidationErrorInterface;

interface ErrorFactoryInterface
{
    /**
     * @param string                     $scope
     * @param ValidationErrorInterface[] $errors
     *
     * @return ErrorInterface
     */
    public function createFromValidationError(string $scope, array $errors): ErrorInterface;
}
