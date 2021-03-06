<?php
declare(strict_types=1);

namespace DIContainer\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class InvalidSingletonException
 * @package DIContainer\Exception
 */
class InvalidSingletonException extends \Exception implements ContainerExceptionInterface
{
    public function __construct()
    {
        parent::__construct(
            'Singleton must be a instantiable class',
            500,
            null);
    }
}
