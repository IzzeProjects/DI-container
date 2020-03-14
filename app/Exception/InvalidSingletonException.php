<?php
declare(strict_types=1);

namespace App\Exception;

/**
 * Class InvalidSingletonException
 * @package App\Exception
 */
class InvalidSingletonException extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            'singleton must be a class',
            500,
            null);
    }
}
