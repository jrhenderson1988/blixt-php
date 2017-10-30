<?php

namespace Blixt\Models;

class Column extends Model
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $indexed;

    /**
     * @var bool
     */
    protected $stored;

    /**
     * @var float
     */
    protected $weight;

    /**
     * Column constructor.
     *
     * @param int|mixed    $id
     * @param string|mixed $name
     * @param bool|mixed   $indexed
     * @param bool|mixed   $stored
     * @param float|mixed  $weight
     */
    public function __construct($id, $name, $indexed = true, $stored = false, $weight = 1.0)
    {
        parent::__construct($id);

        $this->setName($name);
        $this->setIndexed($indexed);
        $this->setStored($stored);
        $this->setWeight($weight);
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string|mixed $name
     */
    public function setName($name)
    {
        $this->name = strval($name);
    }

    /**
     * Tell if this column should be indexed.
     *
     * @return bool
     */
    public function isIndexed()
    {
        return $this->indexed;
    }

    /**
     * Set whether this column should be indexed.
     *
     * @param bool|mixed $indexed
     */
    public function setIndexed($indexed)
    {
        $this->indexed = !! $indexed;
    }

    /**
     * Tell if this column is stored.
     *
     * @return bool
     */
    public function isStored()
    {
        return $this->stored;
    }

    /**
     * Set whether this column is stored.
     *
     * @param bool|mixed $stored
     */
    public function setStored($stored)
    {
        $this->stored = !! $stored;
    }

    /**
     * Set the weight.
     *
     * @param float|mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = floatval($weight);
    }

    /**
     * Get the weight.
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }
}