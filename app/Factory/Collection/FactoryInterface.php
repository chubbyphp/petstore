<?php

declare(strict_types=1);

namespace App\Factory\Collection;

use App\Collection\CollectionInterface;

interface FactoryInterface
{
    /**
     * @return CollectionInterface
     */
    public function create(): CollectionInterface;
}
