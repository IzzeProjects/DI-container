<?php

namespace DIContainer\Tests\Services;

use DIContainer\Entry;
use DIContainer\Exception\ClassNotImplementAbstractionException;
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
    public function testNewInstance()
    {
        $instance = new EntryService(new Entry('string', new \stdClass()));
        $this->assertInstanceOf(EntryService::class, $instance);
    }

    /**
     * @throws ClassNotImplementAbstractionException
     */
    public function testIsImplementationForAbstractionInvalid()
    {
        $entry = new Entry(IdInterface::class, 'string');
        $service = new EntryService($entry);

        $this->assertFalse($service->isImplementationForAbstraction());

        $entry = new Entry(InstantiableValue::class, IdInterface::class);
        $service->setEntry($entry);

        $this->assertFalse($service->isImplementationForAbstraction());
    }

    /**
     * @throws ClassNotImplementAbstractionException
     */
    public function testIsImplementationForAbstractionNotImplement()
    {
        $this->expectException(ClassNotImplementAbstractionException::class);

        $entry = new Entry(IdInterface::class, InstantiableValue::class);
        $service = new EntryService($entry);
        $service->isImplementationForAbstraction();
    }

    /**
     * @throws ClassNotImplementAbstractionException
     */
    public function testIsImplementationForAbstractionValid()
    {

        $entry = new Entry(IdInterface::class, Implementation::class);
        $service = new EntryService($entry);

        $this->assertTrue($service->isImplementationForAbstraction());

        $entry = new Entry(IdAbstract::class, Implementation::class);
        $service->setEntry($entry);

        $this->assertTrue($service->isImplementationForAbstraction());
    }

    public function testIsIdAbstractionValid()
    {
        $entry = new Entry(IdInterface::class, 'string');
        $service = new EntryService($entry);

        $this->assertTrue($service->isIdAbstraction());

        $entry = new Entry(IdAbstract::class, 'string');
        $service->setEntry($entry);

        $this->assertTrue($service->isIdAbstraction());
    }

    public function testIsIdAbstractionInvalid()
    {
        $entry = new Entry('string', 'string');
        $service = new EntryService($entry);

        $this->assertFalse($service->isIdAbstraction());
    }

    public function testIsValueClosureValid()
    {
        $entry = new Entry('string', function () {
        });
        $service = new EntryService($entry);

        $this->assertTrue($service->isValueClosure());
    }

    public function testIsValueClosureInvalid()
    {
        $entry = new Entry('string', 'string');
        $service = new EntryService($entry);

        $this->assertFalse($service->isValueClosure());
    }

    public function testGetIdReflectionClass()
    {
        $entry = new Entry(InstantiableValue::class, 'string');
        $service = new EntryService($entry);

        $this->assertInstanceOf(\ReflectionClass::class, $service->getIdReflectionClass());
    }

    public function testGetIdReflectionClassNull()
    {
        $entry = new Entry('string', 'string');
        $service = new EntryService($entry);

        $this->assertNull($service->getIdReflectionClass());
    }

    public function testGetEntry()
    {
        $entry = new Entry('string', InstantiableValue::class);
        $service = new EntryService($entry);

        $this->assertInstanceOf(Entry::class, $service->getEntry());
    }

    public function testIsValueInstantiableValid()
    {
        $entry = new Entry('string', InstantiableValue::class);
        $service = new EntryService($entry);

        $this->assertTrue($service->isValueInstantiable());
    }

    public function testIsValueInstantiableInvalid()
    {
        $entry = new Entry('string', 'string');
        $service = new EntryService($entry);

        $this->assertFalse($service->isValueInstantiable());
    }

    public function testIsValueNullValid()
    {
        $entry = new Entry('string', null);
        $service = new EntryService($entry);

        $this->assertTrue($service->isValueNull());
    }

    public function testIsValueNullInvalid()
    {
        $entry = new Entry('string', 20.209);
        $service = new EntryService($entry);

        $this->assertFalse($service->isValueNull());
    }

    public function testIsValueObjectValid()
    {
        $entry = new Entry('string', new InstantiableValue());
        $service = new EntryService($entry);

        $this->assertTrue($service->isValueObject());
    }

    public function testIsValueObjectInvalid()
    {
        $entry = new Entry('string', 'string');
        $service = new EntryService($entry);

        $this->assertFalse($service->isValueObject());
    }

    public function testGetValueReflectionClass()
    {
        $entry = new Entry('string', new \stdClass());
        $service = new EntryService($entry);

        $this->assertInstanceOf(\ReflectionClass::class, $service->getValueReflectionClass());
    }

    public function testGetValueReflectionClassNull()
    {
        $entry = new Entry('string', 'string');
        $service = new EntryService($entry);

        $this->assertNull($service->getValueReflectionClass());
    }
}
