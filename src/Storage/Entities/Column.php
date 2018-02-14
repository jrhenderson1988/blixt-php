<?php

namespace Blixt\Storage\Entities;

class Column extends Entity
{
    /**
     * @var int|null
     */
    protected $schemaId;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var bool|null
     */
    protected $isIndexed;

    /**
     * @var bool|null
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
     * @return int|null
     */
    public function getSchemaId()
    {
        return $this->schemaId;
    }

    /**
     * @param int|null|mixed $schemaId
     */
    public function setSchemaId($schemaId)
    {
        $this->schemaId = $schemaId !== null ? intval($schemaId) : null;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null|mixed $name
     */
    public function setName($name)
    {
        $this->name = $name !== null ? strval($name) : null;
    }

    /**
     * @return bool|null
     */
    public function isIndexed()
    {
        return $this->isIndexed;
    }

    /**
     * @param bool|null|mixed $isIndexed
     */
    public function setIndexed($isIndexed)
    {
        $this->isIndexed = $isIndexed !== null ? !! $isIndexed : null;
    }

    /**
     * @return bool|null
     */
    public function isStored()
    {
        return $this->isStored;
    }

    /**
     * @param bool|null|mixed $isStored
     */
    public function setStored($isStored)
    {
        $this->isStored = $isStored !== null ? !! $isStored : null;
    }
}