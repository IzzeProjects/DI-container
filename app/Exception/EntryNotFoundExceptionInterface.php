<?php
declare(strict_types=1);

namespace App\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class EntryNotFoundExceptionInterface
 * @package App\Exception
 * @todo Add message
 */
class EntryNotFoundExceptionInterface extends \Exception implements NotFoundExceptionInterface
{

}
