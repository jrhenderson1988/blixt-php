<?php

namespace Blixt\Storage\Entities;

class Document extends Entity
{
    /**
     * @var int
     */
    protected $schemaId;

    /**
     * @var mixed
     */
    protected $key;

    /**
     * Document constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $schemaId
     * @param mixed     $key
     */
    public function __construct($id, $schemaId, $key)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setKey($key);
    }

    /**
     * @return int|mixed
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
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}