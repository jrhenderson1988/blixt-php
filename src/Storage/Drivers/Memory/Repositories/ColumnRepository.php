<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\ColumnRepository as ColumnRepositoryInterface;

class ColumnRepository extends AbstractRepository implements ColumnRepositoryInterface
{
    const TABLE = 'columns';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_NAME = 'name';
    const FIELD_IS_INDEXED = 'is_indexed';
    const FIELD_IS_STORED = 'is_stored';

    /**
     * @inheritdoc
     */
    protected function map($key, array $row)
    {
        return new Column(
            $key,
            $row[static::FIELD_SCHEMA_ID],
            $row[static::FIELD_NAME],
            $row[static::FIELD_IS_INDEXED],
            $row[static::FIELD_IS_STORED]
        );
    }

    /**
     * @inheritdoc
     */
    public function create(Schema $schema, $name, $isIndexed, $isStored)
    {
        return $this->insert([
            static::FIELD_SCHEMA_ID => $schema->getId(),
            static::FIELD_NAME => $name,
            static::FIELD_IS_INDEXED => $isIndexed,
            static::FIELD_IS_STORED => $isStored
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function save(Column $column)
    {
        $attributes = $this->getAttributesFor($column);

        return $column->exists()
            ? $this->update($column->getId(), $attributes)
            : $this->insert($attributes);
    }

    protected function getAttributesFor(Column $column)
    {
        return [
            static::FIELD_SCHEMA_ID => $column->getSchemaId(),
            static::FIELD_NAME => $column->getName(),
            static::FIELD_IS_INDEXED => $column->isIndexed(),
            static::FIELD_IS_STORED => $column->isStored()
        ];
    }
}