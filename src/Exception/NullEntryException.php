<?php
declare(strict_types=1);

namespace DIContainer\Exception;

/**
 * Class NullEntryException
 * @package DIContainer\Exception
 */
class NullEntryException extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            'Given entry is null',
            500,
            null);
    }
}
