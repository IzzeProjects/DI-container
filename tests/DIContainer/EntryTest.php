<?php
declare(strict_types=1);

namespace DIContainer\Tests;

use DIContainer\Entry;
use DIContainer\Exception\NullEntryException;
use PHPUnit\Framework\TestCase;

/**
 * Class EntryTest
 * @package DIContainer\Tests
 */
class EntryTest extends TestCase
{

    protected string $id = 'id';
    protected string $value = 'value';

    /**
     * @return Entry
     * @throws NullEntryException
     */
    public function testNewInstanceValid(): Entry
    {
        $entry = new Entry($this->id, $this->value);

        $this->assertInstanceOf(Entry::class, $entry);

        return $entry;
    }

    public function testNewInstanceNullValue(): void
    {
        $this->expectException(NullEntryException::class);
        new Entry('test', null);
    }

    /**
     * @depends testNewInstanceValid
     * @param Entry $entry
     */
    public function testGetId(Entry $entry): void
    {
        $this->assertEquals($this->id, $entry->getId());
    }

    /**
     * @depends testNewInstanceValid
     * @param Entry $entry
     */
    public function testIsSingleton(Entry $entry): void
    {
        $this->assertFalse($entry->isSingleton());
    }

    /**
     * @depends testNewInstanceValid
     * @param Entry $entry
     */
    public function testGetValue(Entry $entry): void
    {
        $this->assertEquals($this->value, $entry->getValue());
    }

    /**
     * @depends testNewInstanceValid
     * @param Entry $entry
     */
    public function testSetValue(Entry $entry): void
    {
        $entry->setValue('string');

        $this->assertEquals('string', $entry->getValue());
    }
}
