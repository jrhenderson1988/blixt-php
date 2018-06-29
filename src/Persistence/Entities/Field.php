<?php

namespace Blixt\Persistence\Entities;

use Blixt\Persistence\Entities\Concerns\BelongsToColumn;
use Blixt\Persistence\Entities\Concerns\BelongsToDocument;

class Field extends Entity
{
    use BelongsToDocument, BelongsToColumn;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Field constructor.
     *
     * @param int|null $id
     * @param int      $documentId
     * @param int      $columnId
     * @param mixed    $value
     */
    public function __construct(?int $id, int $documentId, int $columnId, $value)
    {
        parent::__construct($id);

        $this->setDocumentId($documentId);
        $this->setColumnId($columnId);
        $this->setValue($value);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * Factory method to create a new Field.
     *
     * @param int   $documentId
     * @param int   $columnId
     * @param mixed $value
     *
     * @return \Blixt\Persistence\Entities\Field
     */
    public static function create(int $documentId, int $columnId, $value): Field
    {
        return new static(null, $documentId, $columnId, $value);
    }

    /**
     * Factory method to make a Field from an existing record.
     *
     * @param int   $id
     * @param int   $documentId
     * @param int   $columnId
     * @param mixed $value
     *
     * @return \Blixt\Persistence\Entities\Field
     */
    public static function make(int $id, int $documentId, int $columnId, $value): Field
    {
        return new static($id, $documentId, $columnId, $value);
    }
}