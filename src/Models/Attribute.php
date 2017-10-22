<?php

namespace Blixt\Models;

class Attribute extends Model
{
    /**
     * @var int
     */
    protected $documentId;

    /**
     * @var int
     */
    protected $columnId;

    /**
     * @var mixed
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
    public function __construct($id, $documentId, $columnId, $value)
    {
        parent::__construct($id);

        $this->setDocumentId($documentId);
        $this->setColumnId($columnId);
        $this->setValue($value);
    }

    /**
     * Set the Document ID.
     *
     * @param int|mixed $documentId
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = intval($documentId);
    }

    /**
     * Get the Document ID.
     *
     * @return int
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * Set the Column ID.
     *
     * @param int|mixed $columnId
     */
    public function setColumnId($columnId)
    {
        $this->columnId = intval($columnId);
    }

    /**
     * Get the Column ID.
     *
     * @return int
     */
    public function getColumnId()
    {
        return $this->columnId;
    }

    /**
     * Set the value.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Set the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}