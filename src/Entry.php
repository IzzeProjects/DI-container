<?php
declare(strict_types=1);

namespace DIContainer;

use DIContainer\Exception\NullEntryException;

/**
 * Entry for container
 *
 * Class Entry
 * @package DIContainer
 */
final class Entry
{
    /** @var string ID of entry */
    private string $id;
    /** @var mixed Value of entry */
    private $value;
    /** @var bool Is singleton ? */
    private bool $isSingleton = false;

    /**
     * Entry constructor.
     * @param string $id
     * @param mixed $value
     * @throws NullEntryException
     */
    public function __construct(string $id, $value)
    {
        if ($value === null) {
            throw new NullEntryException();
        }
        $this->id = $id;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->isSingleton;
    }

    /**
     * @param bool $isSingleton
     */
    public function setIsSingleton(bool $isSingleton): void
    {
        $this->isSingleton = $isSingleton;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
