<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\ModelInterface;

interface ModelFactoryInterface
{
    public function create(): ModelInterface;

    public function getClass(): string;
}
