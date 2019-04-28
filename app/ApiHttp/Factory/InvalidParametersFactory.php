<?php

declare(strict_types=1);

namespace App\ApiHttp\Factory;

use Chubbyphp\Validation\Error\ErrorInterface;

final class InvalidParametersFactory implements InvalidParametersFactoryInterface
{
    /**
     * @param ErrorInterface[] $errors
     *
     * @return array
     */
    public function createInvalidParameters(array $errors): array
    {
        $invalidParamaters = [];
        foreach ($errors as $error) {
            $invalidParamaters[] = [
                'name' => $error->getPath(),
                'reason' => $error->getKey(),
                'details' => $error->getArguments(),
            ];
        }

        return $invalidParamaters;
    }
}
