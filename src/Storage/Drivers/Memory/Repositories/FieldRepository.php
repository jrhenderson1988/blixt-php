<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Entity;
use Blixt\Storage\Entities\Field;
use Blixt\Storage\Repositories\FieldRepository as FieldRepositoryInterface;

class FieldRepository extends AbstractRepository implements FieldRepositoryInterface
{
    const ENTITY = Field::class;
    const TABLE = 'fields';
    const FIELD_DOCUMENT_ID = 'document_id';
    const FIELD_COLUMN_ID = 'column_id';
    const FIELD_VALUE = 'value';

    /**
     * @param \Blixt\Storage\Entities\Field $field
     *
     * @return \Blixt\Storage\Entities\Field
     * @throws \Blixt\Exceptions\StorageException
     */
    public function save(Field $field)
    {
        return $this->saveEntity($field);
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
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return array
     */
    protected function getAttributes(Entity $entity)
    {
        return [
            static::FIELD_DOCUMENT_ID => $entity->getDocumentId(),
            static::FIELD_COLUMN_ID => $entity->getColumnId(),
            static::FIELD_VALUE => $entity->getValue()
        ];
    }
}