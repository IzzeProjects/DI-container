<?php
declare(strict_types=1);

namespace App\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class EntryNotFoundExceptionInterface
 * @package App\Exception
 */
class EntryNotFoundExceptionInterface extends \Exception implements NotFoundExceptionInterface
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
