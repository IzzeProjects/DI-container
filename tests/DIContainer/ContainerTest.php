<?php
declare(strict_types=1);

namespace DIContainer\Tests;

use DIContainer\Container;
use DIContainer\ContainerInterface;
use DIContainer\Exception\ClassNotImplementAbstractionException;
use DIContainer\Exception\ImplementationNotFoundException;
use DIContainer\Exception\InvalidEntryValueException;
use DIContainer\Exception\NullEntryException;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Classes for tests
 */
class Singleton
{
}

interface IdInterface
{
}

abstract class IdAbstract
{
    public SimpleEntry $simpleEntry;

    public function __construct(SimpleEntry $simpleEntry)
    {
        $this->simpleEntry = $simpleEntry;
    }
}

class Implementation extends IdAbstract implements IdInterface
{
}

class EntryResolve
{
    public IdInterface $idI;
    public IdAbstract $idA;

    public function __construct(IdInterface $idI, IdAbstract $idA)
    {
        $this->idI = $idI;
        $this->idA = $idA;
    }
}

class SimpleEntry
{
}

/**
 * Class EntryTest
 * @package DIContainer\Tests
 */
class ContainerTest extends TestCase
{
    public function testNewInstance(): void
    {
        $container = $this->getContainerInstance();

        $this->assertInstanceOf(Container::class, $container);
    }

    public function testGetInvalidId(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $container = $this->getContainerInstance();
        $container->get('string');
    }

    /**
     * @return ContainerInterface
     * @throws NullEntryException
     * @throws InvalidEntryValueException
     */
    public function testBindScalar(): ContainerInterface
    {
        $container = $this->getContainerInstance();

        $container->bindScalar('int', 111);
        $container->bindScalar('float', 20.20);
        $container->bindScalar('bool', true);
        $container->bindScalar('string', 'string');

        $this->assertNull(null);

        return $container;
    }

    /**
     * @depends testBindScalar
     * @param ContainerInterface $container
     */
    public function testGetPrimitives(ContainerInterface $container): void
    {
        $this->assertIsInt($container->get('int'));
        $this->assertIsFloat($container->get('float'));
        $this->assertIsString($container->get('string'));
        $this->assertIsBool($container->get('bool'));
    }

    /**
     * @throws InvalidEntryValueException
     * @throws NullEntryException
     */
    public function testBindNull(): void
    {
        $this->expectException(NullEntryException::class);

        $container = $this->getContainerInstance();
        $container->bindScalar('null', null);
    }

    /**
     * @return ContainerInterface
     */
    public function testBindClosure(): ContainerInterface
    {
        $container = $this->getContainerInstance();
        $container->bindClosure('closure', function (ContainerInterface $container) {
            return $container;
        });
        $this->assertNull(null);

        return $container;
    }

    /**
     * @depends testBindClosure
     * @param ContainerInterface $container
     */
    public function testGetClosure(ContainerInterface $container): void
    {
        $this->assertInstanceOf(ContainerInterface::class, $container->get('closure'));
    }

    /**
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     */
    public function testGetResolveDependenciesUnresolvable(): void
    {
        $this->expectException(ImplementationNotFoundException::class);

        $container = $this->getContainerInstance();
        $container->bindClass('resolve', EntryResolve::class);
        $container->get('resolve');
    }

    /**
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     */
    public function testGetResolveDependenciesValid(): void
    {
        $container = $this->getContainerInstance();
        $container->bindClass(IdAbstract::class, Implementation::class);
        $container->bindClass(IdInterface::class, Implementation::class);
        $container->bindClass('resolve', EntryResolve::class);
        $container->get('resolve');

        /** @var EntryResolve $resolved */
        $resolved = $container->get('resolve');

        $this->assertInstanceOf(IdAbstract::class, $resolved->idA);
        $this->assertInstanceOf(IdInterface::class, $resolved->idI);
        $this->assertInstanceOf(SimpleEntry::class, $resolved->idA->simpleEntry);
        $this->assertInstanceOf(EntryResolve::class, $container->get('resolve'));
    }

    /**
     * @return ContainerInterface
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     * @throws NullEntryException
     */
    public function testBindBindSingletonValid(): ContainerInterface
    {
        $container = $this->getContainerInstance();
        $container->bindSingleton('singleton', new Singleton());
        $container->bindSingleton('singleton', Singleton::class);

        $this->assertNull(null);

        return $container;
    }

    /**
     * @depends testBindBindSingletonValid
     * @param ContainerInterface $container
     */
    public function testGetSingleton(ContainerInterface $container): void
    {
        $singleton = $container->get('singleton');
        $singleton1 = $container->get('singleton');

        $this->assertEquals(spl_object_id($singleton), spl_object_id($singleton1));
    }

    /**
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     * @throws NullEntryException
     */
    public function testBindSingletonInvalid(): void
    {
        $this->expectException(InvalidEntryValueException::class);

        $container = $this->getContainerInstance();
        $container->bindSingleton('singleton', 'string');
    }

    /**
     * @return ContainerInterface
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     * @throws NullEntryException
     */
    public function testSingletonAbstractionAndImplementationValid(): ContainerInterface
    {
        $container = $this->getContainerInstance();
        $container->bindSingleton(IdAbstract::class, Implementation::class);
        $container->bindSingleton(IdInterface::class, Implementation::class);

        $this->assertNull(null);

        return $container;
    }

    /**
     * @depends testSingletonAbstractionAndImplementationValid
     *
     * @param ContainerInterface $container
     */
    public function testGetSingletonAbstractionAndImplementation(ContainerInterface $container): void
    {
        $idAbstract1 = $container->get(IdAbstract::class);
        $idAbstract2 = $container->get(IdAbstract::class);

        $idInterface1 = $container->get(IdInterface::class);
        $idInterface2 = $container->get(IdInterface::class);

        $this->assertEquals(spl_object_id($idAbstract1), spl_object_id($idAbstract2));
        $this->assertEquals(spl_object_id($idInterface1), spl_object_id($idInterface2));

        $this->assertInstanceOf(IdAbstract::class, $container->get(IdAbstract::class));
        $this->assertInstanceOf(IdInterface::class, $container->get(IdInterface::class));
    }

    /**
     * @return ContainerInterface
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     */
    public function testBindAbstractionAndImplementationValid(): ContainerInterface
    {
        $container = $this->getContainerInstance();
        $container->bindClass(IdAbstract::class, Implementation::class);
        $container->bindClass(IdInterface::class, Implementation::class);

        $this->assertNull(null);

        return $container;
    }

    /**
     * @depends testBindAbstractionAndImplementationValid
     *
     * @param ContainerInterface $container
     */
    public function testGetAbstractionAndImplementation(ContainerInterface $container): void
    {
        $this->assertInstanceOf(IdAbstract::class, $container->get(IdAbstract::class));
        $this->assertInstanceOf(IdInterface::class, $container->get(IdInterface::class));
    }

    /**
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     */
    public function testBindAbstractionAndImplementationInvalid(): void
    {
        $this->expectException(InvalidEntryValueException::class);

        $container = $this->getContainerInstance();
        $container->bindClass(IdAbstract::class, 'string');
    }

    /**
     * @throws ClassNotImplementAbstractionException
     * @throws ImplementationNotFoundException
     * @throws InvalidEntryValueException
     */
    public function testBindAbstractionAndImplementationInvalidImplementation(): void
    {
        $this->expectException(ClassNotImplementAbstractionException::class);

        $container = $this->getContainerInstance();
        $container->bindClass(IdAbstract::class, \stdClass::class);
    }

    /**
     * @throws InvalidEntryValueException
     * @throws NullEntryException
     */
    public function testHasValid(): void
    {
        $container = $this->getContainerInstance();
        $container->bindScalar('id', 'string');

        $this->assertTrue($container->has('id'));
    }

    /**
     * @throws InvalidEntryValueException
     * @throws NullEntryException
     */
    public function testHasInvalid(): void
    {
        $container = $this->getContainerInstance();
        $container->bindScalar('id', 'string');

        $this->assertFalse($container->has('string'));
    }

    /**
     * @return ContainerInterface
     */
    public function getContainerInstance(): ContainerInterface
    {
        return new Container();
    }
}
