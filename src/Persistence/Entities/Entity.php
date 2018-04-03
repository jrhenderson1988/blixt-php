<?php

namespace Blixt\Persistence\Entities;

abstract class Entity
{
    const TABLE = '';
    const FIELD_ID = 'id';

    /**
     * @var int|null
     */
    protected $id;

    /**
     * Entity constructor.
     *
     * @param int|null $id
     */
    public function __construct(?int $id)
    {
        $this->setId($id);
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Tell if this entity exists, that is, whether its ID property is not null.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->getId() !== null;
    }

    /**
     * Get the table for the entity.
     *
     * @return string
     */
    public function getTable()
    {
        return static::TABLE;
    }

    /**
     * Convert this entity to an array for storage.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            static::FIELD_ID => $this->getId()
        ];
    }
}