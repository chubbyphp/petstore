<?php

declare(strict_types=1);

namespace App\Repository;

use App\Collection\CollectionInterface;
use App\Collection\UserCollection;
use App\Model\ModelInterface;
use App\Model\User;
use App\Security\UserInterface;
use App\Security\UserRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

final class UserRepository implements UserRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserCollection|CollectionInterface $userCollection
     */
    public function resolveCollection(CollectionInterface $userCollection): void
    {
        throw new \LogicException('Not implemented');
    }

    /**
     * @return User|UserInterface|null
     */
    public function findById(string $id): ?UserInterface
    {
        return $this->entityManager->find(User::class, $id);
    }

    /**
     * @return User|UserInterface|null
     */
    public function findByUsername(string $username): ?UserInterface
    {
        /** @var EntityRepository $entityRepository */
        $entityRepository = $this->entityManager->getRepository(User::class);

        return $entityRepository->findOneBy(['username' => $username]);
    }

    /**
     * @param User|ModelInterface $user
     */
    public function persist(ModelInterface $user): void
    {
        if (!$user instanceof User) {
            throw new \TypeError(
                sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    User::class,
                    get_class($user)
                )
            );
        }

        $this->entityManager->persist($user);
    }

    /**
     * @param User|ModelInterface $user
     */
    public function remove(ModelInterface $user): void
    {
        if (!$user instanceof User) {
            throw new \TypeError(
                sprintf(
                    '%s() expects parameter 1 to be %s, %s given',
                    __METHOD__,
                    User::class,
                    get_class($user)
                )
            );
        }

        $this->entityManager->remove($user);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
