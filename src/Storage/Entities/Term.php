<?php

namespace Blixt\Storage\Entities;

class Term extends Entity
{
    /**
     * @var int|mixed
     */
    protected $schemaId;

    /**
     * @var int|mixed
     */
    protected $wordId;

    /**
     * @var int|mixed
     */
    protected $documentCount;

    /**
     * Term constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $schemaId
     * @param int|mixed $termId
     * @param int|mixed $documentCount
     */
    public function __construct($id, $schemaId, $termId, $documentCount)
    {
        parent::__construct($id);

        $this->schemaId = $schemaId;
        $this->wordId = $termId;
        $this->documentCount = $documentCount;
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