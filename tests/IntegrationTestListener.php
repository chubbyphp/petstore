<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

final class IntegrationTestListener implements TestListener
{
    use TestListenerDefaultImplementation;

    const PHP_SERVER_PORT = 49199;
    const PHP_SERVER_LOGFILE = 'php-test-server.log';
    const PHP_SERVER_PIDFILE = 'php-test-server.pid';
    const ENV_INTEGRATION_ENDPOINT = 'INTEGRATION_ENDPOINT';

    private $started = false;

    /**
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite)
    {
        if (!$this->isServerNeeded($suite)) {
            return;
        }

        $this->killPhpServer();

        $this->initialize();

        $execute = sprintf(
            '%s > %s 2>&1 & echo $! > %s',
            $this->getCommand(),
            $this->getLogFile(),
            $this->getPidFile()
        );

        exec($execute);

        usleep(100000);

        $this->started = true;
    }

    /**
     * @param TestSuite $suite
     *
     * @return bool
     */
    private function isServerNeeded(TestSuite $suite): bool
    {
        if (false !== getenv(self::ENV_INTEGRATION_ENDPOINT)) {
            return false;
        }

        if (false === strpos($suite->getName(), 'Integration')) {
            return false;
        }

        if ($this->started) {
            return false;
        }

        return true;
    }

    /**
     * @param TestSuite $suite
     */
    public function endTestSuite(TestSuite $suite)
    {
        $this->killPhpServer();
    }

    /**
     * @return string
     */
    private function getCommand(): string
    {
        return sprintf(
            'php -S localhost:%d -t %s %s',
            self::PHP_SERVER_PORT,
            realpath(__DIR__.'/../public'),
            realpath(__DIR__.sprintf('/../public/index_ci.php'))
        );
    }

    private function killPhpServer()
    {
        if (!is_file($this->getPidFile())) {
            return;
        }

        $pid = (int) file_get_contents($this->getPidFile());

        exec(sprintf('/bin/kill %d', $pid));

        unlink($this->getPidFile());
        unlink($this->getLogFile());
    }

    /**
     * @return string
     */
    private function getPidFile(): string
    {
        return sys_get_temp_dir().'/'.self::PHP_SERVER_PIDFILE;
    }

    /**
     * @return string
     */
    private function getLogFile(): string
    {
        return sys_get_temp_dir().'/'.self::PHP_SERVER_LOGFILE;
    }

    private function initialize()
    {
        $consolePath = realpath(__DIR__.'/../bin/console');

        echo 'initialize: start' . PHP_EOL;

        passthru($consolePath.' dbal:database:drop --if-exists --force --env=ci');
        passthru($consolePath.' dbal:database:create --env=ci');
        passthru($consolePath.' orm:schema-tool:update --dump-sql --force --env=ci');

        passthru($consolePath.' config:clean-directories cache log --env=ci');

        echo 'initialize: end' . PHP_EOL . PHP_EOL;
    }
}
