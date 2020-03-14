<?php
declare(strict_types=1);

namespace App\Exception;

/**
 * Class ClassNotImplementAbstractionException
 * @package App\Exception
 */
class ClassNotImplementAbstractionException extends \Exception
{
    public function __construct(string $class, string $interface)
    {
        parent::__construct(
            'Class ' . $class . ' not implement abstraction ' . $interface,
            500,
            null);
    }
}
