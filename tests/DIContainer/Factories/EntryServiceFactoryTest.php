<?php
declare(strict_types=1);

namespace DIContainer\Tests\Factories;

use DIContainer\Factories\EntryServiceFactory;
use DIContainer\Services\EntryService;
use PHPUnit\Framework\TestCase;

/**
 * Class EntryTest
 * @package DIContainer\Tests
 */
class EntryServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        $entry = EntryServiceFactory::create('string', 1);

        $this->assertInstanceOf(EntryService::class, $entry);
    }
}
