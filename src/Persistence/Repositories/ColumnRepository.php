<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Schema;
use Illuminate\Support\Collection;

class ColumnRepository extends Repository
{
    /**
     * Get all columns that belong to the given schema.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBySchema(Schema $schema): Collection
    {
        return $this->driver()->getWhere(Column::class, [
            Column::FIELD_SCHEMA_ID => $schema->getId()
        ]);
    }

    /**
     * Save the given column, updating it in the storage if it already exists, or inserting a new one if it doesn't.
     *
     * @param \Blixt\Persistence\Entities\Column $column
     *
     * @return \Blixt\Persistence\Entities\Column
     */
    public function save(Column $column): Column
    {
        return $column->exists() ? $this->driver()->update($column) : $this->driver()->insert($column);
    }
}