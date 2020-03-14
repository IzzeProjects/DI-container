<?php
declare(strict_types=1);

namespace App\Factories;

use App\Entry;
use App\Services\EntryService;

/**
 * Entry service factory
 *
 * Class EntryServiceFactory
 * @package App\Factories
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
