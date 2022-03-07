<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Runner\BeforeTestHook;

final class PhpServerTestHook implements BeforeTestHook
{
    public const PHP_SERVER_PORT = 49199;
    public const ENV_INTEGRATION_ENDPOINT = 'INTEGRATION_ENDPOINT';

    private ?int $serverPid = null;

    public function __destruct()
    {
        if (null !== $this->serverPid) {
            exec(sprintf('kill %d', $this->serverPid));
        }
    }

    public function executeBeforeTest(string $test): void
    {
        if (null !== $this->serverPid) {
            return;
        }

        if (false !== getenv(self::ENV_INTEGRATION_ENDPOINT)) {
            return;
        }

        if (!$this->isIntegrationTest($test)) {
            return;
        }

        $this->initialize();
        $this->startServer();
    }

    private function isIntegrationTest(string $test): bool
    {
        return str_starts_with($test, 'App\\Tests\\Integration');
    }

    private function initialize(): void
    {
        $consolePath = realpath(__DIR__.'/../bin/console');

        echo 'initialize: start'.PHP_EOL;

        passthru($consolePath.' odm:schema:drop --db --env=phpunit');
        passthru($consolePath.' odm:schema:update --env=phpunit');

        passthru($consolePath.' clean-directories cache log --env=phpunit');

        echo 'initialize: end'.PHP_EOL.PHP_EOL;
    }

    private function startServer(): void
    {
        $command = sprintf(
            'APP_ENV=phpunit php -S localhost:%d -t %s %s',
            self::PHP_SERVER_PORT,
            realpath(__DIR__.'/../public'),
            realpath(__DIR__.'/../public/index.php')
        );

        echo $command.PHP_EOL.PHP_EOL;

        $this->serverPid = (int) exec(sprintf('%s > /dev/null 2>&1 & echo $!', $command));

        while (true) {
            if (\is_resource(@fsockopen('localhost', self::PHP_SERVER_PORT))) {
                break;
            }

            usleep(10000);
        }
    }
}
