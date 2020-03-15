<?php
declare(strict_types=1);

namespace DIContainer\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class NotInstantiableException
 * @package DIContainer\Exception
 */
class NotInstantiableException extends \Exception implements ContainerExceptionInterface
{
    public function __construct(string $entry)
    {
        parent::__construct(
            'Argument ' . $entry . ' is not Instantiable',
            500,
            null
        );
    }
}
