<?php

namespace Blixt\Storage\Entities;

use Blixt\Storage\Entities\Concerns\BelongsToColumn;
use Blixt\Storage\Entities\Concerns\BelongsToDocument;

class Field extends Entity
{
    use BelongsToDocument, BelongsToColumn;

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