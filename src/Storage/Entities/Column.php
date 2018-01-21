<?php

namespace Blixt\Storage\Entities;

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
    protected $isIndexed;

    /**
     * @var bool
     */
    protected $isStored;

    /**
     * Column constructor.
     *
     * @param int|mixed    $id
     * @param int|mixed    $schemaId
     * @param string|mixed $name
     * @param bool|mixed   $isIndexed
     * @param bool|mixed   $isStored
     */
    public function __construct($id, $schemaId, $name, $isIndexed, $isStored)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setName($name);
        $this->setIndexed($isIndexed);
        $this->setStored($isStored);
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
        return $this->isIndexed;
    }

    /**
     * @param bool|mixed $isIndexed
     */
    public function setIndexed($isIndexed)
    {
        $this->isIndexed = !! $isIndexed;
    }

    /**
     * @return bool
     */
    public function isStored()
    {
        return $this->isStored;
    }

    /**
     * @param bool|mixed $isStored
     */
    public function setStored($isStored)
    {
        $this->isStored = !! $isStored;
    }
}