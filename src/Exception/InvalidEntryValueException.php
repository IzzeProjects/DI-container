<?php
declare(strict_types=1);

namespace DIContainer\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class InvalidSingletonException
 * @package DIContainer\Exception
 */
class InvalidEntryValueException extends \Exception implements ContainerExceptionInterface
{
}
