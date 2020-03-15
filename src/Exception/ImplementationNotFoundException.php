<?php
declare(strict_types=1);

namespace DIContainer\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class ImplementationNotFoundException
 * @package DIContainer\Exception
 */
class ImplementationNotFoundException extends \Exception implements ContainerExceptionInterface
{
    public function __construct(string $abstraction)
    {
        parent::__construct(
            'Implementation for ' . $abstraction . ' not found in container',
            500,
            null
        );
    }
}
