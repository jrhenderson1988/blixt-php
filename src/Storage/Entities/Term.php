<?php

namespace Blixt\Storage\Entities;

/**
 * @Entity
 * @Table(
 *     name="terms",
 *     uniqueConstraints={
 *         @UniqueConstraint(name="uq_terms_schema_id_word_id", columns={"schema_id", "word_id"})
 *     }
 * )
 */
class Term
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="integer", name="schema_id")
     * @var int|mixed
     */
    private $schemaId;

    /**
     * @Column(type="integer", name="word_id")
     * @var int|mixed
     */
    private $wordId;

    /**
     * @Column(type="integer", name="document_count")
     * @var int|mixed
     */
    private $documentCount;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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