<?php

namespace Blixt\Persistence\Entities;

abstract class Entity
{
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
}
