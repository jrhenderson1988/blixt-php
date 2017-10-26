<?php

namespace Blixt\Models;

class Term extends Model
{
    /**
     * @var int
     */
    protected $schemaId;

    /**
     * @var int
     */
    protected $wordId;

    /**
     * @var int
     */
    protected $fieldCount;

    /**
     * Term constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $schemaId
     * @param int|mixed $wordId
     * @param int|mixed $fieldCount
     *
     * @internal param mixed|string $name
     */
    public function __construct($id, $schemaId, $wordId, $fieldCount)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setWordId($wordId);
        $this->setFieldCount($fieldCount);
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
     * Set the Schema ID.
     *
     * @param int|mixed $schemaId
     */
    public function setSchemaId($schemaId)
    {
        $this->schemaId = intval($schemaId);
    }

    /**
     * Get the Word ID.
     *
     * @return int
     */
    public function getWordId()
    {
        return $this->wordId;
    }

    /**
     * Get the Word ID.
     *
     * @param int|mixed $wordId
     */
    public function setWordId($wordId)
    {
        $this->wordId = intval($wordId);
    }

    /**
     * Get the field count.
     *
     * @return int
     */
    public function getFieldCount()
    {
        return $this->fieldCount;
    }

    /**
     * Set the field count.
     *
     * @param int|mixed $fieldCount
     */
    public function setFieldCount($fieldCount)
    {
        $this->fieldCount = intval($fieldCount);
    }
}