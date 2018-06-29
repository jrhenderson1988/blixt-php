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
    protected $id;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Record constructor.
     *
     * @param int   $id
     * @param array $attributes
     */
    public function __construct(int $id, array $attributes)
    {
        $this->id = $id;
        $this->attributes = $attributes;
    }

    /**
     * Get the ID.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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