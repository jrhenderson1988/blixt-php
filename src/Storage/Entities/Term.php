<?php

namespace Blixt\Storage\Entities;

class Term extends Entity
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
     */
    public function __construct($id, $schemaId, $wordId, $fieldCount)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setWordId($wordId);
        $this->setFieldCount($fieldCount);
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
     * @return int|mixed
     */
    public function getWordId()
    {
        return $this->wordId;
    }

    /**
     * @param int|mixed $wordId
     */
    public function setWordId($wordId)
    {
        $this->wordId = intval($wordId);
    }

    /**
     * @return int|mixed
     */
    public function getFieldCount()
    {
        return $this->fieldCount;
    }

    /**
     * @param int|mixed $fieldCount
     */
    public function setFieldCount($fieldCount)
    {
        $this->fieldCount = intval($fieldCount);
    }
}