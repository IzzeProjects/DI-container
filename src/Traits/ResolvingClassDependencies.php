<?php
declare(strict_types=1);

namespace DIContainer\Traits;

use DIContainer\Entry;
use DIContainer\Exception\{
    ImplementationNotFoundException,
    NotInstantiableException
};
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

        if ($constructor === null) {
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
        /** @var Entry $entry */
        $entry = $this->entries[$implementation->getName()];

        if ($entry->isSingleton()) {
            if (is_object($entry->getValue())) {
                return $entry->getValue();
            }

            $entry->setValue($this->resolveEntryClassDependencies(new \ReflectionClass($entry->getValue())));

            return $entry->getValue();
        }

        $entry = $this->entries[$implementation->getName()];

        return $this->resolveEntryClassDependencies(new \ReflectionClass($entry->getValue()));
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
