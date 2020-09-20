<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CleanDirectoriesCommand extends Command
{
    /**
     * @var array<string, string>
     */
    private $directories;

    /**
     * @param array<string, string> $directories
     */
    public function __construct(array $directories)
    {
        parent::__construct();

        $this->directories = $directories;
    }

    protected function configure(): void
    {
        $this
            ->setName('clean-directories')
            ->setDescription('Delete everything within a given directory')
            ->addArgument(
                'directoryNames',
                InputArgument::IS_ARRAY,
                'Directory names which should be cleaned'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var array<int, string> $directoryNames */
        $directoryNames = $input->getArgument('directoryNames');

        $unsupportedDirectoryNames = array_diff($directoryNames, array_keys($this->directories));

        if ([] !== $unsupportedDirectoryNames) {
            $output->writeln(
                sprintf('<error>Unsupported directory names: "%s"</error>', implode('", "', $unsupportedDirectoryNames))
            );

            return 1;
        }

        foreach ($directoryNames as $directoryName) {
            $directory = $this->directories[$directoryName];

            $output->writeln(
                sprintf('<info>Start clean directory with name "%s" at path "%s"</info>', $directoryName, $directory)
            );

            $this->cleanDirectory($directory);
        }

        return 0;
    }

    private function cleanDirectory(string $path, bool $rmdir = false): void
    {
        $subPaths = glob($path.'/*');

        if ($subPaths) {
            foreach ($subPaths as $subPath) {
                if (is_dir($subPath)) {
                    $this->cleanDirectory($subPath, true);
                } else {
                    unlink($subPath);
                }
            }
        }

        if ($rmdir) {
            rmdir($path);
        }
    }
}
