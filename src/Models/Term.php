<?php

namespace Blixt\Models;

class Term extends Model
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Term constructor.
     *
     * @param int|mixed    $id
     * @param string|mixed $name
     */
    public function __construct($id, $name)
    {
        parent::__construct($id);

        $this->setName($name);
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
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}