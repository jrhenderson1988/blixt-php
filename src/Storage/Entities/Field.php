<?php

namespace Blixt\Storage\Entities;

/**
 * @ORM\Table(
 *     name="fields",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uq_fields_document_id_column_id", columns={"document_id", "column_id"})
 *     }
 * )
 */
class Field extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="document_id")
     * @var int
     */
    protected $documentId;

    /**
     * @var int
     */
    protected $columnId;

    /**
     * @var mixed|null
     */
    protected $value;

    /**
     * Attribute constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $documentId
     * @param int|mixed $columnId
     * @param mixed     $value
     */
    public function __construct($id, $documentId, $columnId, $value = null)
    {
        parent::__construct($id);

        $this->setDocumentId($documentId);
        $this->setColumnId($columnId);
        $this->setValue($value);
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