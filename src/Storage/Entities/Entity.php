<?php

namespace Blixt\Storage\Entities;

abstract class Entity
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null|mixed $id
     */
    public function setId($id)
    {
        $this->id = $id !== null ? intval($id) : null;
    }

    /**
     * Tell if this entity exists, that is, whether its ID property is not null.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->getId() !== null;
    }
}