<?php

namespace Blixt\Entities;

class Document extends Entity
{
    /**
     * @var mixed
     */
    protected $key;

    /**
     * Document constructor.
     *
     * @param int|mixed $id
     * @param mixed     $key
     */
    public function __construct($id, $key)
    {
        parent::__construct($id);

        $this->setKey($key);
    }

    /**
     * Get the key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the key.
     *
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}