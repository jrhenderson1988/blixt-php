<?php

namespace Blixt\Persistence;

/**
 * Class Record
 *
 * A simple class that wraps around records returned by the storage drivers.
 *
 * @package Blixt\Persistence
 */
class Record
{
    /**
     * @var int
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Record constructor.
     *
     * @param int   $identifier
     * @param array $attributes
     */
    public function __construct(int $identifier, array $attributes)
    {
        $this->identifier = $identifier;
        $this->attributes = $attributes;
    }

    /**
     * Get the identifier.
     *
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }

    /**
     * Get the attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}