<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\ModelInterface;

interface ModelFactoryInterface
{
    /**
     * @return ModelInterface
     */
    public function create(): ModelInterface;

    /**
     * @return string
     */
    public function getClass(): string;
}
