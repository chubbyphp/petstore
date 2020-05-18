<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findById(string $id): ?UserInterface;

    public function findByUsername(string $username): ?UserInterface;
}
