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
    protected $documentCount;

    /**
     * Term constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $schemaId
     * @param int|mixed $wordId
     * @param int|mixed $documentCount
     */
    public function __construct($id, $schemaId, $wordId, $documentCount)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setWordId($wordId);
        $this->setDocumentCount($documentCount);
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
    public function getDocumentCount()
    {
        return $this->documentCount;
    }

    /**
     * @param int|mixed $documentCount
     */
    public function setDocumentCount($documentCount)
    {
        $this->documentCount = intval($documentCount);
    }
}