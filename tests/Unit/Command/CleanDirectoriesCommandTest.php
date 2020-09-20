<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\CleanDirectoriesCommand;
use App\Tests\AssertTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \App\Command\CleanDirectoriesCommand
 *
 * @internal
 */
final class CleanDirectoriesCommandTest extends TestCase
{
    use AssertTrait;

    public function testGetName(): void
    {
        $command = new CleanDirectoriesCommand([]);

        self::assertSame('clean-directories', $command->getName());
    }

    public function testWithUnsupportedDirectoryNames(): void
    {
        $path = sys_get_temp_dir().'/'.uniqid('clean-directories-');
        $cacheDir = $path.'/cache';

        $input = new ArrayInput([
            'directoryNames' => ['cache', 'log'],
        ]);

        $output = new BufferedOutput();

        $command = new CleanDirectoriesCommand(['cache' => $cacheDir]);

        self::assertSame(1, $command->run($input, $output));

        $outputMessage = <<<'EOT'
Unsupported directory names: "log"

EOT;

        self::assertSame($outputMessage, $output->fetch());
    }

    public function testWithSupportedDirectoryNames(): void
    {
        $path = sys_get_temp_dir().'/'.uniqid('clean-directories-');
        $cacheDir = $path.'/cache';
        $logDir = $path.'/log';

        $input = new ArrayInput([
            'directoryNames' => ['cache', 'log'],
        ]);

        $output = new BufferedOutput();

        mkdir($cacheDir.'/some/value/to/clean', 0777, true);
        touch($cacheDir.'/some/value/to/clean/file');
        mkdir($logDir.'/another/value/to/clean', 0777, true);
        touch($logDir.'/another/value/to/clean/file');

        $command = new CleanDirectoriesCommand(['cache' => $cacheDir, 'log' => $logDir]);

        $code = $command->run($input, $output);

        self::assertDirectoryDoesNotExist($cacheDir.'/some');
        self::assertDirectoryDoesNotExist($logDir.'/another');

        rmdir($cacheDir);
        rmdir($logDir);

        self::assertSame(0, $code);

        $outputMessage = <<<'EOT'
Start clean directory with name "cache" at path "%s"
Start clean directory with name "log" at path "%s"

EOT;

        self::assertSame(sprintf($outputMessage, $cacheDir, $logDir), $output->fetch());
    }
}
