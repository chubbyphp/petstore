<?php

declare(strict_types=1);

namespace App\Factory;

use App\Collection\CollectionInterface;

interface CollectionFactoryInterface
{
    /**
     * @return CollectionInterface
     */
    public function create(): CollectionInterface;
}
