<?php

namespace Blixt\Entities;

class Field extends Entity
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
     * Get the Document ID.
     *
     * @return int
     */
    public function getDocumentId()
    {
        return $this->documentId;
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
     * Get the Column ID.
     *
     * @return int
     */
    public function getColumnId()
    {
        return $this->columnId;
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
     * Set the value.
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = is_null($value) || $value === '' ? null : $value;
    }
}