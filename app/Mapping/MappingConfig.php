<?php

declare(strict_types=1);

namespace App\Mapping;

final class MappingConfig
{
    /**
     * @var string
     */
    private $mappingClass;

    /**
     * @var array<string>
     */
    private $dependencies = [];

    /**
     * @param string        $mappingClass
     * @param array<string> $dependencies
     */
    public function __construct(string $mappingClass, array $dependencies = [])
    {
        $this->mappingClass = $mappingClass;

        foreach ($dependencies as $dependency) {
            $this->addDependency($dependency);
        }
    }

    public function getMappingClass(): string
    {
        return $this->mappingClass;
    }

    /**
     * @return array<string>
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    private function addDependency(string $dependency): void
    {
        $this->dependencies[] = $dependency;
    }
}
