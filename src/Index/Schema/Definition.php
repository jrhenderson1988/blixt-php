<?php

namespace Blixt\Index\Schema;

class Definition
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
     * Definition constructor.
     *
     * @param string|mixed $name
     * @param bool|mixed   $indexed
     * @param bool|mixed   $stored
     */
    public function __construct($name, $indexed = true, $stored = false)
    {
        $this->name = strval($name);
        $this->indexed = !! $indexed;
        $this->stored = !! $stored;
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
}