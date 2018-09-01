<?php

declare(strict_types=1);

namespace App\Factory\Model;

use App\Model\ModelInterface;

interface FactoryInterface
{
    /**
     * @return ModelInterface
     */
    public function create(): ModelInterface;
}
