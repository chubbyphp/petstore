<?php

declare(strict_types=1);

namespace App\Core\Dto\Collection;

use App\Core\Collection\CollectionInterface;

interface CollectionRequestInterface
{
    public function createCollection(): CollectionInterface;
}
