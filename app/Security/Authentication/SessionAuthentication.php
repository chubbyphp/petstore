<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Security\UserRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use PSR7Sessions\Storageless\Session\SessionInterface;

final class SessionAuthentication implements AuthenticationInterface
{
    public const SESSION_KEY = 'u';
    public const ID_KEY = 'i';
    public const HASH_KEY = 'h';

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getType(): string
    {
        return 'session';
    }

    public function isResponsible(ServerRequestInterface $request): bool
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        return $session->has(self::SESSION_KEY);
    }

    public function isAuthenticated(ServerRequestInterface $request): bool
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $data = $session->get(self::SESSION_KEY);

        if (!isset($data[self::ID_KEY])
            || !is_string($data[self::ID_KEY])
            || !isset($data[self::HASH_KEY])
            || !is_string($data[self::HASH_KEY])) {
            return false;
        }

        if (null === $user = $this->userRepository->findById($data[self::ID_KEY])) {
            return false;
        }

        if ($data[self::HASH_KEY] !== $user->getHash()) {
            return false;
        }

        return true;
    }
}
