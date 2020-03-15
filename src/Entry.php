<?php
declare(strict_types=1);

namespace DIContainer;

/**
 * Entry for container
 *
 * Class Entry
 * @package DIContainer
 */
class Entry
{
    /** @var string ID of entry */
    private string $id;
    /** @var mixed Value of entry */
    private $value;
    /** @var bool Is singleton ? */
    private bool $isSingleton;

    /**
     * Entry constructor.
     * @param string $id
     * @param mixed $value
     * @param bool $isSingleton
     */
    public function __construct(string $id, $value, bool $isSingleton = false)
    {
        $this->id = $id;
        $this->value = $value;
        $this->isSingleton = $isSingleton;
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
}
