<?php

declare(strict_types=1);

namespace App\Core\Dto\Model;

use App\Core\Model\ModelInterface;

interface ModelRequestInterface
{
    public function createModel(): ModelInterface;

    public function updateModel(ModelInterface $model): ModelInterface;
}
