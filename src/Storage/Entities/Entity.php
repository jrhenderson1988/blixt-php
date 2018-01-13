<?php

namespace Blixt\Storage\Entities;

abstract class Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * Model constructor.
     *
     * @param int|mixed $id
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * @param int|mixed $id
     */
    public function setId($id)
    {
        $this->id = intval($id);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}