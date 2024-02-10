<?php

declare(strict_types=1);

namespace App\Dto\Collection;

use App\Collection\CollectionInterface;

interface CollectionRequestInterface
{
    public function createCollection(): CollectionInterface;
}
