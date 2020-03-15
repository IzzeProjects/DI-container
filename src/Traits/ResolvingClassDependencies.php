<?php
declare(strict_types=1);

namespace DIContainer\Traits;

use DIContainer\Exception\ImplementationNotFoundException;
use DIContainer\Exception\NotInstantiableException;
use Psr\Container\ContainerInterface;

/**
 * Trait ClassDependenciesResolving
 * @package DIContainer\Traits
 *
 * @mixin ContainerInterface
 */
trait ResolvingClassDependencies
{
    /**
     * Resolve recursively entry dependencies
     * @param \ReflectionClass $entry
     * @return object
     * @throws ImplementationNotFoundException
     * @throws NotInstantiableException
     * @throws \ReflectionException
     */
    private function resolveEntryClassDependencies(\ReflectionClass $entry): object
    {
        if ($this->isAbstraction($entry)) {
            if (!$this->has($entry->getName())) {
                throw new ImplementationNotFoundException($entry->getName());
            }
            return $this->resolveImplementation($entry);
        }

        $constructor = $entry->getConstructor();

        if (is_null($constructor)) {
            return $entry->newInstance();
        }

        $arguments = $constructor->getParameters();

        foreach ($arguments as $argument) {
            $argumentsInstances[] = $this->resolveEntryClassDependencies($argument->getClass());
        }

        return $entry->newInstanceArgs($argumentsInstances ?? []);
    }

    /**
     * Resolve implementation
     * @param \ReflectionClass $implementation
     * @return mixed|null
     * @throws ImplementationNotFoundException
     * @throws NotInstantiableException
     * @throws \ReflectionException
     */
    private function resolveImplementation(\ReflectionClass $implementation)
    {
        $singleton = $this->singletons[$implementation->getName()] ?? null;
        if (!empty($singleton)) {
            if (is_object($singleton)) {
                return $singleton;
            }

            return $this->singletons[$implementation->getName()] = $this->resolveEntryClassDependencies(new \ReflectionClass($singleton));
        }

        $entry = $this->entries[$implementation->getName()];

        return $this->resolveEntryClassDependencies(new \ReflectionClass($entry));
    }

    /**
     * Check if class is abstraction (interface or abstract class)
     *
     * @param \ReflectionClass|null $class
     * @return bool
     */
    private function isAbstraction(\ReflectionClass $class): bool
    {
        return $class->isInterface() || $class->isAbstract();
    }
}
