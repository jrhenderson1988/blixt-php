<?php

namespace Blixt\Documents;

class Field
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var boolean
     */
    protected $indexed;

    /**
     * @var boolean
     */
    protected $stored;


    public function __construct($key, $value, $indexed = true, $stored = true)
    {
        $this->setKey($key);
        $this->setValue($key);
        $this->setIndexed($key);
        $this->setStored($key);
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the key.
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = strval($key);
    }

    /**
     * Get the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the values.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Tell if the field should be indexed.
     *
     * @return bool
     */
    public function isIndexed()
    {
        return $this->indexed;
    }

    /**
     * Set if the field should be indexed.
     *
     * @param bool $indexed
     */
    public function setIndexed($indexed)
    {
        $this->indexed = !! $indexed;
    }

    /**
     * Tell if the field should be stored.
     *
     * @return bool
     */
    public function isStored()
    {
        return $this->stored;
    }

    /**
     * Set whether the field should be stored.
     *
     * @param bool $stored
     */
    public function setStored($stored)
    {
        $this->stored = !! $stored;
    }



}