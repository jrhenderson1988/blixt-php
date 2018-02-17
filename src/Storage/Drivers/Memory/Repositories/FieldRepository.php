<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Field;
use Blixt\Storage\Repositories\FieldRepository as FieldRepositoryInterface;

class FieldRepository implements FieldRepositoryInterface
{
    const TABLE = 'fields';
    const FIELD_DOCUMENT_ID = 'document_id';
    const FIELD_COLUMN_ID = 'column_id';
    const FIELD_VALUE = 'value';

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /**
     * FieldRepository constructor.
     *
     * @param \Blixt\Storage\Drivers\Memory\Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param \Blixt\Storage\Entities\Field $field
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function save(Field $field)
    {
        return $field->exists() ? $this->update($field) : $this->create($field);
    }

    /**
     * @param \Blixt\Storage\Entities\Field $field
     *
     * @return \Blixt\Storage\Entities\Field
     */
    protected function create(Field $field)
    {
        $attributes = $this->getAttributes($field);

        $id = $this->storage->insert(static::TABLE, $attributes);

        return $this->map($id, $attributes);
    }

    /**
     * @param \Blixt\Storage\Entities\Field $field
     *
     * @return \Blixt\Storage\Entities\Field
     */
    protected function update(Field $field)
    {
        $attributes = $this->getAttributes($field);

        $this->storage->update(static::TABLE, $field->getId(), $attributes);

        return $field;
    }

    /**
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Field
     */
    protected function map($key, array $row)
    {
        return new Field(
            $key,
            $row[static::FIELD_DOCUMENT_ID],
            $row[static::FIELD_COLUMN_ID],
            $row[static::FIELD_VALUE]
        );
    }

    /**
     * @param \Blixt\Storage\Entities\Field $field
     *
     * @return array
     */
    protected function getAttributes(Field $field)
    {
        return [
            static::FIELD_DOCUMENT_ID => $field->getDocumentId(),
            static::FIELD_COLUMN_ID => $field->getColumnId(),
            static::FIELD_VALUE => $field->getValue()
        ];
    }
}