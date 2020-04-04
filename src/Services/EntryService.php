<?php
declare(strict_types=1);

namespace DIContainer\Services;

use DIContainer\Entry;
use DIContainer\Exception\ClassNotImplementAbstractionException;

/**
 * Service for Entry
 *
 * Class EntryService
 * @package App
 *
 */
final class EntryService
{
    /** @var Entry Entry */
    private ?Entry $entry = null;

    private ?\ReflectionClass $idClass = null;

    private ?\ReflectionClass $valueClass = null;

    /**
     * Check if entry value NULL
     *
     * @return bool
     */
    public function isValueNull(): bool
    {
        return $this->entry->getValue() === null;
    }

    /**
     * Check if entry value is instantiable
     *
     * @return bool
     */
    public function isValueInstantiable(): bool
    {
        if (is_string($this->entry->getValue()) && class_exists($this->entry->getValue())) {
            $class = $this->getValueReflectionClass();

            if ($class !== null && $class->isInstantiable()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if entry value is object
     *
     * @return bool
     */
    public function isValueObject(): bool
    {
        return is_object($this->entry->getValue());
    }

    /**
     * Check if entry value is abstraction (interface ot abstract class)
     *
     * @return bool
     */
    public function isIdAbstraction(): bool
    {
        if (is_string($this->entry->getId())) {
            $class = $this->getIdReflectionClass();

            if ($class !== null && ($class->isAbstract() || $class->isInterface())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if entry value is instantiable
     *
     * @return bool
     */
    public function isValueClosure(): bool
    {
        return $this->entry->getValue() instanceof \Closure;
    }

    /**
     * Check if entry value is scalar
     *
     * @return bool
     */
    public function isValueScalar(): bool
    {
        return is_scalar($this->entry->getValue());
    }

    /**
     * Return id ReflectionClass or null
     *
     * @return \ReflectionClass|null
     */
    public function getIdReflectionClass(): ?\ReflectionClass
    {
        if ($this->idClass !== null) {
            return $this->idClass;
        }

        try {
            return $this->idClass = new \ReflectionClass($this->entry->getId());
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * Return value ReflectionClass or null
     *
     * @return \ReflectionClass|null
     */
    public function getValueReflectionClass(): ?\ReflectionClass
    {
        if ($this->valueClass !== null) {
            return $this->valueClass;
        }

        try {
            return $this->valueClass = new \ReflectionClass($this->entry->getValue());
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * Check if given implementation of abstraction
     *
     * @return bool
     * @throws ClassNotImplementAbstractionException
     */
    public function isImplementationForAbstraction(): bool
    {
        $abstraction = $this->getIdReflectionClass();

        $implementationClass = $this->getValueReflectionClass();

        if ($abstraction === null || $implementationClass === null) {
            return false;
        }

        if (!$this->isIdAbstraction() || (!$this->isValueInstantiable() && !$this->isValueObject())) {
            return false;
        }

        if ($abstraction->isAbstract()) {
            if ($implementationClass->isSubclassOf($abstraction->getName())) {
                return true;
            }
            throw new ClassNotImplementAbstractionException(
                $abstraction->getName(),
                $implementationClass->getName()
            );
        }

        if ($abstraction->isInterface()) {
            if ($implementationClass->implementsInterface($abstraction->getName())) {
                return true;
            }
            throw new ClassNotImplementAbstractionException(
                $abstraction->getName(),
                $implementationClass->getName()
            );
        }

        return false;
    }

    /**
     * @return Entry
     */
    public function getEntry(): Entry
    {
        return $this->entry;
    }

    /**
     * @param Entry $entry
     * @return EntryService
     */
    public function setEntry(Entry $entry): self
    {
        $this->entry = $entry;
        $this->valueClass = null;
        $this->idClass = null;
        return $this;
    }
}
