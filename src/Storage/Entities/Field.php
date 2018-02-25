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

    /**
     * Fluent getter/setter for value.
     *
     * @param null|mixed $value
     *
     * @return $this|mixed|null
     */
    public function value($value = null)
    {
        if (func_num_args() === 0) {
            return $this->getValue();
        }

        $this->setValue($value);

        return $this;
    }

    /**
     * Create a new field from the set of attributes given.
     *
     * @param array|object $attributes
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public static function make($attributes)
    {
        $field = new static();

        foreach ((array) $attributes as $key => $value) {
            if (in_array($key, ['id', 'setId'])) {
                $field->setId($value);
            } elseif (in_array($key, ['document_id', 'documentId', 'setDocumentId'])) {
                $field->setDocumentId($value);
            } elseif (in_array($key, ['column_id', 'columnId', 'setColumnId'])) {
                $field->setColumnId($value);
            } elseif (in_array($key, ['value' ,'setValue'])) {
                $field->setValue($value);
            }
        }

        return $field;
    }
}