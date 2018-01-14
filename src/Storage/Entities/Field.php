<?php

namespace Blixt\Storage\Entities;

/**
 * @Entity
 * @Table(
 *     name="fields",
 *     uniqueConstraints={
 *         @UniqueConstraint(name="uq_fields_document_id_column_id", columns={"document_id", "column_id"})
 *     }
 * )
 */
class Field
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="integer", name="document_id")
     * @var int
     */
    private $documentId;

    /**
     * @Column(type="integer", name="column_id")
     * @var int
     */
    private $columnId;

    /**
     * @Column(type="text", nullable=true)
     * @var mixed|null
     */
    private $value;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param int|mixed $documentId
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = intval($documentId);
    }

    /**
     * @return int
     */
    public function getColumnId()
    {
        return $this->columnId;
    }

    /**
     * @param int|mixed $columnId
     */
    public function setColumnId($columnId)
    {
        $this->columnId = intval($columnId);
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = is_null($value) || $value === '' ? null : $value;
    }
}