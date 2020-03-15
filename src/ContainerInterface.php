<?php
declare(strict_types=1);

namespace DIContainer;

use Psr\Container\ContainerInterface as PSRContainerInterface;

/**
 * Container interface
 * This interface extends PSR
 *
 * Interface ContainerInterface
 * @package DIContainer
 */
interface ContainerInterface extends PSRContainerInterface
{
    /**
     * Bind an entry to container
     *
     * @param string $id Singleton identifier
     * @param mixed $value Any type excluding NULL
     * @return $this
     */
    public function bind(string $id, $value): self;

    /**
     * Bind a singleton to container
     *
     * @param string $id Singleton identifier
     * @param string|object $value
     * @return $this
     */
    public function singleton(string $id, $value): self;
}
