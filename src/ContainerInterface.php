<?php
declare(strict_types=1);

namespace DIContainer;

use DIContainer\Exception\{
    ClassNotImplementAbstractionException,
    ImplementationNotFoundException,
    InvalidEntryValueException,
    NullEntryException
};
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * DI container interface
 *
 * Interface ContainerInterface
 * @package DIContainer
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Bind a scalar value to container
     *
     * @param string $id
     * @param int|float|bool|string $value
     * @return ContainerInterface
     * @throws InvalidEntryValueException
     * @throws NullEntryException
     */
    public function bindScalar(string $id, $value): self;

    /**
     * Bind an array to container
     *
     * @param string $id
     * @param array $value
     * @return ContainerInterface
     */
    public function bindArray(string $id, array $value): self;

    /**
     * Bind a class to container
     *
     * @param string $id
     * @param string $value
     * @return ContainerInterface
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     */
    public function bindClass(string $id, string $value): self;

    /**
     * Bind a singleton to
     *
     * @param string $id
     * @param string|object $value
     * @return ContainerInterface
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     * @throws NullEntryException
     */
    public function bindSingleton(string $id, $value): self;

    /**
     * Bind a closure to container
     *
     * @param string $id
     * @param \Closure $value
     * @return $this
     */
    public function bindClosure(string $id, \Closure $value): self;
}
