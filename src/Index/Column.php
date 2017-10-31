<?php

namespace Blixt\Index;

class Column
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
     * @param string|mixed $name
     * @param bool|mixed   $indexed
     * @param bool|mixed   $stored
     * @param float|mixed  $weight
     */
    public function __construct($name, $indexed, $stored, $weight)
    {
        $this->name = strval($name);
        $this->indexed = !! $indexed;
        $this->stored = !! $stored;
        $this->weight = floatval($weight);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isIndexed()
    {
        return $this->indexed;
    }

    /**
     * @return bool
     */
    public function isStored()
    {
        return $this->stored;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }
}