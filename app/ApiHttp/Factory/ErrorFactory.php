<?php

declare(strict_types=1);

namespace App\ApiHttp\Factory;

use Chubbyphp\Validation\Error\ErrorInterface as ValidationErrorInterface;
use Chubbyphp\Validation\Error\NestedErrorMessages;

final class ErrorFactory implements ErrorFactoryInterface
{
    /**
     * @param ValidationErrorInterface[] $errors
     *
     * @return array
     */
    public function createErrorMessages(array $errors): array
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

        return $nestedErrorMessages->getMessages();
    }
}
