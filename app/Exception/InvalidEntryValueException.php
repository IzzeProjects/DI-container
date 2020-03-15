<?php
declare(strict_types=1);

namespace App\Exception;

/**
 * Class InvalidEntryValueException
 * @package App\Exception
 */
class InvalidEntryValueException extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            'Entry value is object. A object is singleton in container - use ContainerInterface::singleton',
            500,
            null);
    }
}
