<?php

namespace Blixt\Storage\Entities;

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
    public function __construct($id = null)
    {
        $this->setId($id);
    }

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
     * Fluent getter/setter for id.
     *
     * @param int|null|mixed $id
     *
     * @return $this|int|null
     */
    public function id($id = null)
    {
        if (func_num_args() === 0) {
            return $this->getId();
        }

        $this->setId($id);

        return $this;
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