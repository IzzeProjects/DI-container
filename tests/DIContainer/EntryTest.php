<?php
declare(strict_types=1);

namespace DIContainer\Tests;

use DIContainer\Entry;
use PHPUnit\Framework\TestCase;

/**
 * Class EntryTest
 * @package DIContainer\Tests
 */
class EntryTest extends TestCase
{
    protected Entry $entry;

    protected string $testId = 'testId';

    protected string $testValue = 'testValue';

    protected function setUp(): void
    {
        $this->entry = new Entry($this->testId, $this->testValue);
    }

    public function testNewInstance()
    {
        $entry = new Entry('test', 'test entry', true);

        $this->assertInstanceOf(Entry::class, $entry);
    }

    /**
     * @depends testNewInstance
     */
    public function testGetId()
    {
        $this->assertEquals($this->testId, $this->entry->getId());
    }

    /**
     * @depends testNewInstance
     */
    public function testIsSingleton()
    {
        $this->assertEquals($this->testValue, $this->entry->getValue());
    }

    /**
     * @depends testNewInstance
     */
    public function testGetValue()
    {
        $this->assertFalse($this->entry->isSingleton());
    }
}
