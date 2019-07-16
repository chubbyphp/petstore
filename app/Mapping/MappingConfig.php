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
     * @var string[]
     */
    private $dependencies = [];

    /**
     * @param string   $mappingClass
     * @param string[] $dependencies
     */
    public function __construct(string $mappingClass, array $dependencies = [])
    {
        $this->mappingClass = $mappingClass;

        foreach ($dependencies as $dependency) {
            $this->addDependency($dependency);
        }
    }

    /**
     * @return string
     */
    public function getMappingClass(): string
    {
        return $this->mappingClass;
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @param string $dependency
     */
    private function addDependency(string $dependency): void
    {
        $this->dependencies[] = $dependency;
    }
}
