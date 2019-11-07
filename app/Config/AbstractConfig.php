<?php

declare(strict_types=1);

namespace App\Config;

use Chubbyphp\Config\ConfigInterface;

abstract class AbstractConfig implements ConfigInterface
{
    /**
     * @var string
     */
    protected $rootDir;

    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function getDirectories(): array
    {
        return [
            'cache' => $this->getCacheDir(),
            'log' => $this->getLogDir(),
        ];
    }

    protected function getCacheDir(): string
    {
        return $this->rootDir.'/var/cache/'.$this->getEnv();
    }

    protected function getLogDir(): string
    {
        return $this->rootDir.'/var/log/'.$this->getEnv();
    }
}
