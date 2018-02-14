<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Document;
use Blixt\Storage\Entities\Field;
use Blixt\Storage\Repositories\FieldRepository as FieldRepositoryInterface;

class FieldRepository extends AbstractRepository implements FieldRepositoryInterface
{
    const TABLE = 'fields';
    const FIELD_DOCUMENT_ID = 'document_id';
    const FIELD_COLUMN_ID = 'column_id';
    const FIELD_VALUE = 'value';

    /**
     * @inheritdoc
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
     * @param \Blixt\Storage\Entities\Document $document
     * @param int|mixed                        $columnId
     * @param string|mixed|null                $value
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function create(Document $document, $columnId, $value = null)
    {
        return $this->insert([
            static::FIELD_DOCUMENT_ID => $document->getId(),
            static::FIELD_COLUMN_ID => $columnId,
            static::FIELD_VALUE => $value ? $value : null,
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Field $field
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function save(Field $field)
    {
        // TODO: Implement save() method.
    }
}