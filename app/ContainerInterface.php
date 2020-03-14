<?php
declare(strict_types=1);

namespace App;

use Psr\Container\ContainerInterface as PSRContainerInterface;

/**
 * Container interface
 * This interface extends PSR
 *
 * Interface ContainerInterface
 * @package App
 */
interface ContainerInterface extends PSRContainerInterface
{
    /**
     * Bind an entry to container
     *
     * @param string $id Singleton identifier
     * @param mixed $value Any type excepts NULL
     * @param bool $isSingleton If true and value is not class -  must be thrown SingletonNotClassException
     * @return $this
     */
    public function bind(string $id, $value, bool $isSingleton = false): self;
}
