<?php
declare(strict_types=1);

namespace DIContainer;

use DIContainer\Exception\{
    EntryNotFoundException,
    ImplementationNotFoundException,
    InvalidEntryValueException,
};
use Psr\Container\{
    ContainerExceptionInterface,
    NotFoundExceptionInterface
};
use DIContainer\Services\EntryService;
use DIContainer\Traits\ResolvingClassDependencies;

/**
 * DI container
 *
 * Class Container
 * @package DIContainer
 */
final class Container implements ContainerInterface
{
    use ResolvingClassDependencies;

    /** @var Entry[] Array of entries */
    private array $entries = [];

    private EntryService $entryService;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->entryService = new EntryService();
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws \ReflectionException
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new EntryNotFoundException($id);
        }

        $this->entryService->setEntry($this->entries[$id]);

        /**
         * Closures always takes container as argument
         */
        if ($this->entryService->isValueClosure()) {
            $closure = $this->entryService->getEntry()->getValue();
            return $closure($this);
        }

        /**
         * If entry is instantiable resolve dependencies and return an instance
         */
        if ($this->entryService->isValueInstantiable()) {
            $object = $this->resolveEntryClassDependencies(
                $this->entryService->getValueReflectionClass()
            );

            if ($this->entryService->getEntry()->isSingleton()) {
                $entry = $this->entryService->getEntry();
                $entry->setValue($object);
            }

            return $object;
        }

        return $this->entryService->getEntry()->getValue();
    }

    /**
     * Returns true if the container can return an entry or singleton for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->entries);
    }

    /**
     * @inheritDoc
     */
    public function bindScalar(string $id, $value): self
    {
        $this->entryService->setEntry(new Entry($id, $value));

        if (!$this->entryService->isValueScalar()) {
            throw new InvalidEntryValueException('Value must be a scalar value');
        }

        $this->addEntry($this->entryService->getEntry());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bindArray(string $id, array $value): self
    {
        $this->entryService->setEntry(new Entry($id, $value));

        $this->addEntry($this->entryService->getEntry());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bindClass(string $id, string $value): self
    {
        $this->entryService->setEntry(new Entry($id, $value));

        if (!$this->entryService->isValueInstantiable()) {
            throw new InvalidEntryValueException('Class must be instantiable');
        }

        if ($this->entryService->isIdAbstraction() && !$this->entryService->isImplementationForAbstraction()) {
            throw new ImplementationNotFoundException($this->entryService->getEntry()->getId());
        }

        $this->addEntry($this->entryService->getEntry());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bindSingleton(string $id, $value): self
    {
        $entry = new Entry($id, $value);

        $entry->setIsSingleton(true);

        $this->entryService->setEntry($entry);

        if ($this->entryService->isValueObject()) {
            $this->addEntry($this->entryService->getEntry());
            return $this;
        }

        if (!$this->entryService->isValueInstantiable()) {
            throw new InvalidEntryValueException('Singleton must be instantiable');
        }

        if ($this->entryService->isIdAbstraction() && !$this->entryService->isImplementationForAbstraction()) {
            throw new ImplementationNotFoundException($this->entryService->getEntry()->getId());
        }

        $this->addEntry($this->entryService->getEntry());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bindClosure(string $id, \Closure $value): self
    {
        $this->entryService->setEntry(new Entry($id, $value));

        $this->addEntry($this->entryService->getEntry());

        return $this;
    }

    /**
     * Add entry to array
     *
     * @param Entry $entry
     */
    private function addEntry(Entry $entry): void
    {
        $this->entries[$entry->getId()] = $entry;
    }
}
