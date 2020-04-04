<?php
declare(strict_types=1);

namespace DIContainer\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class NullEntryException
 * @package DIContainer\Exception
 */
class NullEntryException extends \Exception implements ContainerExceptionInterface
{
    public function __construct()
    {
        parent::__construct(
            'Given entry is null',
            500,
            null);
    }
}
