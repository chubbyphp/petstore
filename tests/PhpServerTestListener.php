<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

final class PhpServerTestListener implements TestListener
{
    use TestListenerDefaultImplementation;

    const PHP_SERVER_PORT = 49199;
    const ENV_INTEGRATION_ENDPOINT = 'INTEGRATION_ENDPOINT';

    /**
     * @var int|null
     */
    private $serverPid;

    public function __destruct()
    {
        if (null !== $this->serverPid) {
            exec(sprintf('kill %d', $this->serverPid));
        }
    }

    /**
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite): void
    {
        if (null !== $this->serverPid) {
            return;
        }

        if (false !== getenv(self::ENV_INTEGRATION_ENDPOINT)) {
            return;
        }

        if (!$this->isIntegrationTest($suite)) {
            return;
        }

        $this->initialize();
        $this->startServer();
    }

    /**
     * @param TestSuite $suite
     *
     * @return bool
     */
    private function isIntegrationTest(TestSuite $suite): bool
    {
        $name = $suite->getName();

        if ('Integration' === $name) {
            return true;
        }

        if (false !== strpos($name, 'Integration')) {
            return true;
        }

        return false;
    }

    private function initialize(): void
    {
        $consolePath = realpath(__DIR__.'/../bin/console');

        echo 'initialize: start'.PHP_EOL;

        passthru($consolePath.' dbal:database:drop --if-exists --force --env=phpunit');
        passthru($consolePath.' dbal:database:create --env=phpunit');
        passthru($consolePath.' orm:schema-tool:update --dump-sql --force --env=phpunit');

        passthru($consolePath.' config:clean-directories cache log --env=phpunit');

        echo 'initialize: end'.PHP_EOL.PHP_EOL;
    }

    private function startServer(): void
    {
        $command = sprintf(
            'php -S localhost:%d -t %s %s',
            self::PHP_SERVER_PORT,
            realpath(__DIR__.'/../public'),
            realpath(__DIR__.'/../public/index_phpunit.php')
        );

        echo $command.PHP_EOL.PHP_EOL;

        $this->serverPid = (int) exec(sprintf('%s > /dev/null 2>&1 & echo $!', $command));

        while (true) {
            if (is_resource(@fsockopen('localhost', self::PHP_SERVER_PORT))) {
                break;
            }

            usleep(10000);
        }
    }
}
