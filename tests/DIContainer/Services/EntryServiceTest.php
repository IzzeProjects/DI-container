<?php

namespace DIContainer\Tests\Services;

use DIContainer\Entry;
use DIContainer\Exception\ClassNotImplementAbstractionException;
use DIContainer\Exception\NullEntryException;
use DIContainer\Services\EntryService;
use PHPUnit\Framework\TestCase;

class InstantiableValue
{
}

interface IdInterface
{
}

abstract class IdAbstract
{
}

class Implementation extends IdAbstract implements IdInterface
{
}

/**
 * Class EntryServiceTest
 * @package DIContainer\Tests\Services
 */
class EntryServiceTest extends TestCase
{
    protected EntryService $entryService;

    protected function setUp(): void
    {
        $this->entryService = new EntryService();
    }

    /**
     * @throws ClassNotImplementAbstractionException
     * @throws NullEntryException
     */
    public function testIsImplementationForAbstractionInvalid(): void
    {
        $entry = new Entry(IdInterface::class, 'string');
        $this->entryService->setEntry($entry);

        $this->assertFalse($this->entryService->isImplementationForAbstraction());

        $entry = new Entry(InstantiableValue::class, IdInterface::class);
        $this->entryService->setEntry($entry);

        $this->assertFalse($this->entryService->isImplementationForAbstraction());
    }

    /**
     * @throws ClassNotImplementAbstractionException
     * @throws NullEntryException
     */
    public function testIsImplementationForAbstractionNotImplement(): void
    {
        $this->expectException(ClassNotImplementAbstractionException::class);

        $entry = new Entry(IdInterface::class, InstantiableValue::class);
        $this->entryService->setEntry($entry);
        $this->entryService->isImplementationForAbstraction();
    }

    /**
     * @throws ClassNotImplementAbstractionException
     * @throws NullEntryException
     */
    public function testIsImplementationForAbstractionValid(): void
    {

        $entry = new Entry(IdInterface::class, Implementation::class);
        $this->entryService->setEntry($entry);

        $this->assertTrue($this->entryService->isImplementationForAbstraction());

        $entry = new Entry(IdAbstract::class, Implementation::class);
        $this->entryService->setEntry($entry);

        $this->assertTrue($this->entryService->isImplementationForAbstraction());
    }

    public function testIsIdAbstractionValid(): void
    {
        $entry = new Entry(IdInterface::class, 'string');
        $this->entryService->setEntry($entry);

        $this->assertTrue($this->entryService->isIdAbstraction());

        $entry = new Entry(IdAbstract::class, 'string');
        $this->entryService->setEntry($entry);

        $this->assertTrue($this->entryService->isIdAbstraction());
    }

    public function testIsIdAbstractionInvalid(): void
    {
        $entry = new Entry('string', 'string');
        $this->entryService->setEntry($entry);

        $this->assertFalse($this->entryService->isIdAbstraction());
    }

    public function testIsValueClosureValid(): void
    {
        $entry = new Entry('string', function () {
        });
        $this->entryService->setEntry($entry);

        $this->assertTrue($this->entryService->isValueClosure());
    }

    public function testIsValueClosureInvalid(): void
    {
        $entry = new Entry('string', 'string');
        $this->entryService->setEntry($entry);

        $this->assertFalse($this->entryService->isValueClosure());
    }

    public function testGetIdReflectionClass(): void
    {
        $entry = new Entry(InstantiableValue::class, 'string');
        $this->entryService->setEntry($entry);

        $this->assertInstanceOf(\ReflectionClass::class, $this->entryService->getIdReflectionClass());
    }

    public function testGetIdReflectionClassNull(): void
    {
        $entry = new Entry('string', 'string');
        $this->entryService->setEntry($entry);

        $this->assertNull($this->entryService->getIdReflectionClass());
    }

    public function testGetEntry(): void
    {
        $entry = new Entry('string', InstantiableValue::class);
        $this->entryService->setEntry($entry);

        $this->assertInstanceOf(Entry::class, $this->entryService->getEntry());
    }

    public function testIsValueInstantiableValid(): void
    {
        $entry = new Entry('string', InstantiableValue::class);
        $this->entryService->setEntry($entry);

        $this->assertTrue($this->entryService->isValueInstantiable());
    }

    public function testIsValueInstantiableInvalid(): void
    {
        $entry = new Entry('string', 'string');
        $this->entryService->setEntry($entry);

        $this->assertFalse($this->entryService->isValueInstantiable());
    }

    /**
     * @throws NullEntryException
     */
    public function testIsValueNullInvalid(): void
    {
        $entry = new Entry('string', 20.209);
        $this->entryService->setEntry($entry);

        $this->assertFalse($this->entryService->isValueNull());
    }

    /**
     * @throws NullEntryException
     */
    public function testIsValueObjectValid(): void
    {
        $entry = new Entry('string', new InstantiableValue());
        $this->entryService->setEntry($entry);

        $this->assertTrue($this->entryService->isValueObject());
    }

    public function testIsValueObjectInvalid(): void
    {
        $entry = new Entry('string', 'string');
        $this->entryService->setEntry($entry);

        $this->assertFalse($this->entryService->isValueObject());
    }

    /**
     * @throws NullEntryException
     */
    public function testIsValueScalarValid(): void
    {
        $entry = new Entry('string', 111);
        $this->entryService->setEntry($entry);

        $this->assertTrue($this->entryService->isValueScalar());
    }

    /**
     * @throws NullEntryException
     */
    public function testIsValueScalarInvalid(): void
    {
        $entry = new Entry('string', new \stdClass());
        $this->entryService->setEntry($entry);

        $this->assertFalse($this->entryService->isValueScalar());
    }

    /**
     * @throws NullEntryException
     */
    public function testGetValueReflectionClass(): void
    {
        $entry = new Entry('string', new \stdClass());
        $this->entryService->setEntry($entry);

        $this->assertInstanceOf(\ReflectionClass::class, $this->entryService->getValueReflectionClass());
    }

    public function testGetValueReflectionClassNull(): void
    {
        $entry = new Entry('string', 'string');
        $this->entryService->setEntry($entry);

        $this->assertNull($this->entryService->getValueReflectionClass());
    }
}
