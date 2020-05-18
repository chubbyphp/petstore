<?php

declare(strict_types=1);

namespace App\Security\Authentication;

final class PasswordManager implements PasswordManagerInterface
{
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verify(string $password, string $persistedPassword): bool
    {
        return password_verify($password, $persistedPassword);
    }
}
