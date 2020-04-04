<?php
declare(strict_types=1);

namespace DIContainer\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class ClassNotImplementAbstractionException
 * @package DIContainer\Exception
 */
class ClassNotImplementAbstractionException extends \Exception implements ContainerExceptionInterface
{
    public function __construct(string $class, string $interface)
    {
        parent::__construct(
            'Class ' . $class . ' not implement abstraction ' . $interface,
            500,
            null);
    }
}
