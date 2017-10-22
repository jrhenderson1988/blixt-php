<?php

namespace Blixt\Models;

abstract class Model
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
     * Set the ID.
     *
     * @param int|mixed $id
     */
    public function setId($id)
    {
        $this->id = intval($id);
    }

    /**
     * Get the ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}