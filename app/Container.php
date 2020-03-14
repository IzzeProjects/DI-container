<?php
declare(strict_types=1);

namespace App;

use App\Exception\{
    ClassNotImplementAbstractionException,
    EntryNotFoundExceptionInterface,
    ImplementationNotFoundException,
    NullEntryException,
    InvalidSingletonException
};
use Psr\Container\{
    ContainerExceptionInterface,
    NotFoundExceptionInterface
};
use App\Factories\EntryServiceFactory;
use App\Services\EntryService;
use App\Traits\ClassDependenciesResolving;

/**
 * DI container
 *
 * Class Container
 * @package App
 */
final class Container implements ContainerInterface
{
    use ClassDependenciesResolving;

    /** @var array Array of entries. Key of array is identifier of entry, value of array is value of entry */
    private array $entries = [];

    /** @var array Array of singleton. Key of array is identifier of entry, value of array is value of entry */
    private array $singletons = [];

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
            throw new EntryNotFoundExceptionInterface();
        }

        $entryService = $this->getEntryServiceInstance($id);

        /**
         * Closures always takes container as argument
         */
        if ($entryService->isValueClosure()) {
            $closure = $entryService->getEntry()->getValue();
            return $closure($this);
        }

        /**
         * If entry is instantiable resolve dependencies and return instance
         */
        if ($entryService->isValueInstantiable()) {
            $entry = $this->resolveEntryClassDependencies(
                $entryService->getValueReflectionClass()
            );

            if ($entryService->getEntry()->isSingleton()) {
                $this->singletons[$id] = $entry;
            }

            return $entry;
        }

        return $entryService->getEntry()->getValue();
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
    public function has($id)
    {
        return array_key_exists($id, $this->entries) || array_key_exists($id, $this->singletons);
    }

    /**
     * Bind entry to container
     *
     * @param string $id
     * @param mixed $value
     * @param bool $isSingleton
     * @return Container
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidSingletonException
     * @throws NullEntryException
     */
    public function bind(string $id, $value, bool $isSingleton = false): self
    {
        $entryService = EntryServiceFactory::create($id, $value);

        if ($entryService->isValueNull()) {
            throw new NullEntryException();
        }

        if ($entryService->isIdAbstraction()) {
            if (!$entryService->isImplementationForAbstraction()) {
                throw new ImplementationNotFoundException($entryService->getEntry()->getId());
            }
        }

        if ($isSingleton) {
            if ($entryService->isValueInstantiable() || is_object($entryService->getEntry()->getValue())) {
                $this->addSingleton($id, $value);
            } else {
                throw new InvalidSingletonException();
            }
        } else {
            $this->addEntry($id, $value);
        }

        return $this;
    }

    /**
     * Add entry to array
     *
     * @param string $id
     * @param mixed $entry
     */
    private function addEntry(string $id, $entry): void
    {
        $this->entries[$id] = $entry;
    }

    /**
     * Add singleton to array
     *
     * @param string $id
     * @param $singleton
     */
    private function addSingleton(string $id, $singleton): void
    {
        $this->singletons[$id] = $singleton;
    }

    /**
     * Get entry service
     *
     * @param string $id
     * @return EntryService
     */
    private function getEntryServiceInstance(string $id)
    {
        $singleton = $this->singletons[$id] ?? null;

        if (!is_null($singleton)) {
            return EntryServiceFactory::create($id, $singleton, true);
        }

        return EntryServiceFactory::create($id, $this->entries[$id]);
    }
}
