<?php
declare(strict_types=1);

namespace DIContainer\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class EntryNotFoundExceptionInterface
 * @package DIContainer\Exception
 */
class EntryNotFoundException extends \Exception implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct(
            'Entry with id \'' . $id . '\' not found',
            500,
            null
        );
    }
}
