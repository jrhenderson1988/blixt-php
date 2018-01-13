<?php

namespace Blixt\Storage\Entities;

class Schema extends Entity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param int|mixed $id
     * @param string    $name
     */
    public function __construct($id, $name)
    {
        parent::__construct($id);

        $this->setName($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|mixed $name
     */
    public function setName($name)
    {
        $this->name = strval($name);
    }
}