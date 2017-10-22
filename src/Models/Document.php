<?php

namespace Blixt\Models;

class Document extends Model
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
     * Set the Schema ID.
     *
     * @param int|mixed $schemaId
     */
    public function setSchemaId($schemaId)
    {
        $this->schemaId = intval($schemaId);
    }

    /**
     * Get the Schema ID.
     *
     * @return int
     */
    public function getSchemaId()
    {
        return $this->schemaId;
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

    /**
     * Get the key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }
}