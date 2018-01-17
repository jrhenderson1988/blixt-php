<?php

namespace Blixt\Storage\Entities;

interface Term
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int|mixed $id
     */
    public function setId($id);

    /**
     * @return int|mixed
     */
    public function getSchemaId();

    /**
     * @param int|mixed $schemaId
     */
    public function setSchemaId($schemaId);

    /**
     * @return int|mixed
     */
    public function getWordId();

    /**
     * @param int|mixed $wordId
     */
    public function setWordId($wordId);

    /**
     * @return int|mixed
     */
    public function getDocumentCount();

    /**
     * @param int|mixed $documentCount
     */
    public function setDocumentCount($documentCount);
}