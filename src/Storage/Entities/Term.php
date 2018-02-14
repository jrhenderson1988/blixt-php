<?php

namespace Blixt\Storage\Entities;

class Term extends Entity
{
    /**
     * @var int|null
     */
    protected $schemaId;

    /**
     * @var int|null
     */
    protected $wordId;

    /**
     * @var int|null
     */
    protected $fieldCount;

    /**
     * Term constructor.
     *
     * @param int|null|mixed $id
     * @param int|null|mixed $schemaId
     * @param int|null|mixed $wordId
     * @param int|null|mixed $fieldCount
     */
    public function __construct($id = null, $schemaId = null, $wordId = null, $fieldCount = null)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setWordId($wordId);
        $this->setFieldCount($fieldCount);
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
     * @return int|null|mixed
     */
    public function getWordId()
    {
        return $this->wordId;
    }

    /**
     * @param int|null|mixed $wordId
     */
    public function setWordId($wordId)
    {
        $this->wordId = $wordId !== null ? intval($wordId) : null;
    }

    /**
     * @return int|null|mixed
     */
    public function getFieldCount()
    {
        return $this->fieldCount;
    }

    /**
     * @param int|null|mixed $fieldCount
     */
    public function setFieldCount($fieldCount)
    {
        $this->fieldCount = $fieldCount !== null ? intval($fieldCount) : null;
    }
}