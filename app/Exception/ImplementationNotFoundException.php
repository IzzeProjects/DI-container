<?php
declare(strict_types=1);

namespace App\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class ImplementationNotFoundException
 * @package App\Exception
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
