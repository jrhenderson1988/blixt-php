<?php

namespace Blixt\Storage\Entities;

// TODO - Consider adding column boost/weight
class Column extends Entity
{
    /**
     * @var int
     */
    protected $schemaId;

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
     * Column constructor.
     *
     * @param int|mixed    $id
     * @param int|mixed    $schemaId
     * @param string|mixed $name
     * @param bool|mixed   $indexed
     * @param bool|mixed   $stored
     */
    public function __construct($id, $schemaId, $name, $indexed = true, $stored = false)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setName($name);
        $this->setIndexed($indexed);
        $this->setStored($stored);
    }

    /**
     * @return int
     */
    public function getSchemaId()
    {
        return $this->schemaId;
    }

    /**
     * @param int|mixed $schemaId
     */
    public function setSchemaId($schemaId)
    {
        $this->schemaId = intval($schemaId);
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

    /**
     * @return bool
     */
    public function isIndexed()
    {
        return $this->indexed;
    }

    /**
     * @param bool|mixed $indexed
     */
    public function setIndexed($indexed)
    {
        $this->indexed = !! $indexed;
    }

    /**
     * @return bool
     */
    public function isStored()
    {
        return $this->stored;
    }

    /**
     * @param bool|mixed $stored
     */
    public function setStored($stored)
    {
        $this->stored = !! $stored;
    }
}