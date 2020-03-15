<?php
declare(strict_types=1);

namespace DIContainer\Tests;

use DIContainer\Container;
use DIContainer\ContainerInterface;
use DIContainer\Exception\ClassNotImplementAbstractionException;
use DIContainer\Exception\ImplementationNotFoundException;
use DIContainer\Exception\InvalidSingletonException;
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
    public function testNewInstance()
    {
        $container = $this->getContainerInstance();

        $this->assertInstanceOf(Container::class, $container);
    }

    /**
     * @return ContainerInterface
     */
    public function testBindPrimitives(): ContainerInterface
    {
        $container = $this->getContainerInstance();
        $container->bind('array', [1, 2, 3]);
        $container->bind('float', 20.20);
        $container->bind('int', 123);
        $container->bind('string', 'string');
        $container->bind('bool', true);
        $container->bind('object', new \stdClass());

        $this->assertNull(null);

        return $container;
    }

    public function testGetInvalidId()
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $container = $this->getContainerInstance();
        $container->get('string');
    }


    /**
     * @depends testBindPrimitives
     * @param ContainerInterface $container
     */
    public function testGetPrimitives(ContainerInterface $container)
    {
        $this->assertIsArray($container->get('array'));
        $this->assertIsFloat($container->get('float'));
        $this->assertIsInt($container->get('int'));
        $this->assertIsString($container->get('string'));
        $this->assertIsBool($container->get('bool'));
        $this->assertIsObject($container->get('object'));
    }

    public function testBindNull()
    {
        $this->expectException(NullEntryException::class);

        $container = $this->getContainerInstance();
        $container->bind('null', null);
    }

    /**
     * @return ContainerInterface
     */
    public function testBindClosure(): ContainerInterface
    {
        $container = $this->getContainerInstance();
        $container->bind('closure', function (ContainerInterface $container) {
            return $container;
        });
        $this->assertNull(null);

        return $container;
    }

    /**
     * @depends testBindClosure
     * @param ContainerInterface $container
     */
    public function testGetClosure(ContainerInterface $container)
    {
        $this->assertInstanceOf(ContainerInterface::class, $container->get('closure'));
    }

    public function testGetResolveDependenciesUnresolvable()
    {
        $this->expectException(ImplementationNotFoundException::class);

        $container = $this->getContainerInstance();
        $container->bind('resolve', EntryResolve::class);
        $container->get('resolve');
    }


    public function testGetResolveDependenciesValid()
    {
        $container = $this->getContainerInstance();
        $container->bind(IdAbstract::class, Implementation::class);
        $container->bind(IdInterface::class, Implementation::class);
        $container->bind('resolve', EntryResolve::class);
        $container->get('resolve');

        /** @var EntryResolve $resolved */
        $resolved = $container->get('resolve');

        $this->assertInstanceOf(IdAbstract::class, $resolved->idA);
        $this->assertInstanceOf(IdInterface::class, $resolved->idI);
        $this->assertInstanceOf(SimpleEntry::class, $resolved->idA->simpleEntry);

        $this->assertInstanceOf(EntryResolve::class, $container->get('resolve'));
    }

    public function testBindResource()
    {
        $container = $this->getContainerInstance();
        $stdin = fopen('php://stdin', 'r');
        $container->bind('id', $stdin);
        $this->assertNull(null);
    }

    /**
     * @return ContainerInterface
     */
    public function testBindBindSingletonValid(): ContainerInterface
    {
        $container = $this->getContainerInstance();
        $container->singleton('singleton', new Singleton());
        $container->singleton('singleton', Singleton::class);

        $this->assertNull(null);

        return $container;
    }

    /**
     * @depends testBindBindSingletonValid
     * @param ContainerInterface $container
     */
    public function testGetSingleton(ContainerInterface $container)
    {
        $singleton = $container->get('singleton');
        $singleton1 = $container->get('singleton');

        $this->assertEquals(spl_object_id($singleton), spl_object_id($singleton1));
    }

    public function testBindSingletonInvalid()
    {
        $this->expectException(InvalidSingletonException::class);

        $container = $this->getContainerInstance();
        $container->singleton('singleton', 'string');
    }

    /**
     * @return ContainerInterface
     */
    public function testSingletonAbstractionAndImplementationValid(): ContainerInterface
    {
        $container = $this->getContainerInstance();
        $container->singleton(IdAbstract::class, Implementation::class);
        $container->singleton(IdInterface::class, Implementation::class);

        $this->assertNull(null);

        return $container;
    }

    /**
     * @depends testSingletonAbstractionAndImplementationValid
     *
     * @param ContainerInterface $container
     */
    public function testGetSingletonAbstractionAndImplementation(ContainerInterface $container)
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
     */
    public function testBindAbstractionAndImplementationValid(): ContainerInterface
    {
        $container = $this->getContainerInstance();
        $container->bind(IdAbstract::class, Implementation::class);
        $container->bind(IdInterface::class, Implementation::class);

        $this->assertNull(null);

        return $container;
    }

    /**
     * @depends testBindAbstractionAndImplementationValid
     *
     * @param ContainerInterface $container
     */
    public function testGetAbstractionAndImplementation(ContainerInterface $container)
    {
        $this->assertInstanceOf(IdAbstract::class, $container->get(IdAbstract::class));
        $this->assertInstanceOf(IdInterface::class, $container->get(IdInterface::class));
    }

    public function testBindAbstractionAndImplementationInvalid()
    {
        $this->expectException(ImplementationNotFoundException::class);

        $container = $this->getContainerInstance();
        $container->bind(IdAbstract::class, 'string');
    }

    public function testBindAbstractionAndImplementationInvalidImplementation()
    {
        $this->expectException(ClassNotImplementAbstractionException::class);

        $container = $this->getContainerInstance();
        $container->bind(IdAbstract::class, \stdClass::class);
    }

    public function testHasValid()
    {
        $container = $this->getContainerInstance();
        $container->bind('id', 'string');

        $this->assertTrue($container->has('id'));
    }

    public function testHasInvalid()
    {
        $container = $this->getContainerInstance();
        $container->bind('id', 'string');

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
