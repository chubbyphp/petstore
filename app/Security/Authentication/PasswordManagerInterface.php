<?php

declare(strict_types=1);

namespace App\Security\Authentication;

interface PasswordManagerInterface
{
    public function hash(string $password): string;

    public function verify(string $password, string $persistedPassword): bool;
}
