<?php

namespace Blixt\Storage\Entities;

class Document extends Entity
{
    /**
     * @var int|null
     */
    protected $schemaId;

    /**
     * @var null|mixed
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
     * @return int|null|mixed
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
     * @return null|mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param null|mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}