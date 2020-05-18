<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\User;
use App\Security\Authentication\PasswordManagerInterface;
use App\Security\UserRepositoryInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateUserCommand extends Command
{
    /**
     * @var PasswordManagerInterface
     */
    private $passwordManager;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        PasswordManagerInterface $passwordManager,
        UserRepositoryInterface $userRepository,
        ValidatorInterface $validator
    ) {
        parent::__construct('petstore:create:user');

        $this->passwordManager = $passwordManager;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::REQUIRED);
        $this->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setUsername($input->getArgument('username'));
        $user->setPassword($this->passwordManager->hash($input->getArgument('password')));

        if ([] !== $errors = $this->validator->validate($user)) {
            foreach ($errors as $error) {
                $output->writeln(sprintf('<error>%s: %s</error>', $error->getPath(), $error->getKey()));
            }

            return 1;
        }

        $this->userRepository->persist($user);
        $this->userRepository->flush();

        return 0;
    }
}
