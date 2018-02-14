<?php

namespace Blixt\Storage\Entities;

class Field extends Entity
{
    /**
     * @var int|null
     */
    protected $documentId;

    /**
     * @var int|null
     */
    protected $columnId;

    /**
     * @var null|mixed
     */
    protected $value;

    /**
     * Field constructor.
     *
     * @param int|null|mixed $id
     * @param int|null|mixed $documentId
     * @param int|null|mixed $columnId
     * @param null|mixed     $value
     */
    public function __construct($id = null, $documentId = null, $columnId = null, $value = null)
    {
        parent::__construct($id);

        $this->setDocumentId($documentId);
        $this->setColumnId($columnId);
        $this->setValue($value);
    }

    /**
     * @return int|null
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param int|null|mixed $documentId
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId !== null ? intval($documentId) : null;
    }

    /**
     * @return int|null
     */
    public function getColumnId()
    {
        return $this->columnId;
    }

    /**
     * @param int|null|mixed $columnId
     */
    public function setColumnId($columnId)
    {
        $this->columnId = $columnId !== null ? intval($columnId) : null;
    }

    /**
     * @return null|mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null|mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value !== '' && $value !== null ? $value : null;
    }
}