<?php

declare(strict_types=1);

namespace App\ApiHttp\Factory;

use Chubbyphp\ApiHttp\Error\Error;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\Validation\Error\ErrorInterface as ValidationErrorInterface;
use Chubbyphp\Validation\Error\NestedErrorMessages;

final class ErrorFactory implements ErrorFactoryInterface
{
    /**
     * @param string                     $scope
     * @param ValidationErrorInterface[] $errors
     *
     * @return ErrorInterface
     */
    public function createFromValidationError(string $scope, array $errors): ErrorInterface
    {
        $nestedErrorMessages = new NestedErrorMessages(
            $errors,
            function (string $key, array $arguments) {
                return [
                    'key' => $key,
                    'arguments' => $arguments,
                ];
            }
        );

        return new Error(
            $scope,
            'validation',
            'there are validation errors',
            null,
            $nestedErrorMessages->getMessages()
        );
    }
}
