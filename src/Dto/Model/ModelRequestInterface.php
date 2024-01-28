<?php

declare(strict_types=1);

namespace App\Dto\Model;

use App\Model\ModelInterface;

interface ModelRequestInterface
{
    public function createModel(): ModelInterface;

    public function updateModel(ModelInterface $model): ModelInterface;
}
