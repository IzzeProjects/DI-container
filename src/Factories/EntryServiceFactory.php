<?php
declare(strict_types=1);

namespace DIContainer\Factories;

use DIContainer\Entry;
use DIContainer\Services\EntryService;

/**
 * Entry service factory
 *
 * Class EntryServiceFactory
 * @package DIContainer\Factories
 */
final class EntryServiceFactory
{
    /**
     * @param string $id
     * @param $value
     * @param bool $isSingleton
     * @return EntryService
     */
    public static function create(string $id, $value, bool $isSingleton = false): EntryService
    {
        return new EntryService(new Entry($id, $value, $isSingleton));
    }
}
