<?php

declare(strict_types=1);

namespace App\Factory\Model;

use App\Factory\ModelFactoryInterface;
use App\Model\ModelInterface;
use App\Model\User;

final class UserFactory implements ModelFactoryInterface
{
    /**
     * @return User|ModelInterface
     */
    public function create(): ModelInterface
    {
        return new User();
    }

    public function getClass(): string
    {
        return User::class;
    }
}
