<?php

namespace Blixt\Storage\Entities;

class Field extends Entity
{
    /**
     * @var int
     */
    protected $documentId;

    /**q
     * @var int
     */
    protected $columnId;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Field constructor.
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
        $this->value = $value;
    }
}