<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Repositories\ColumnRepository as ColumnRepositoryInterface;
use Illuminate\Support\Collection;

class ColumnRepository implements ColumnRepositoryInterface
{
    const TABLE = 'columns';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_NAME = 'name';
    const FIELD_IS_INDEXED = 'is_indexed';
    const FIELD_IS_STORED = 'is_stored';

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /**
     * ColumnRepository constructor.
     *
     * @param \Blixt\Storage\Drivers\Memory\Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        $results = new Collection();

        foreach ($this->storage->all(static::TABLE) as $key => $row) {
            $results->put($key, $this->map($key, $row));
        }

        return $results;
    }

    /**
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function save(Column $column)
    {
        return $column->exists() ? $this->update($column) : $this->create($column);
    }

    /**
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return \Blixt\Storage\Entities\Column
     */
    protected function create(Column $column)
    {
        $attributes = $this->getAttributes($column);

        $id = $this->storage->insert(static::TABLE, $attributes);

        return $this->map($id, $attributes);
    }

    /**
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return \Blixt\Storage\Entities\Column
     */
    protected function update(Column $column)
    {
        $attributes = $this->getAttributes($column);

        $this->storage->update(static::TABLE, $column->getId(), $attributes);

        return $column;
    }

    /**
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Column
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
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return array
     */
    protected function getAttributes(Column $column)
    {
        return [
            static::FIELD_SCHEMA_ID => $column->getSchemaId(),
            static::FIELD_NAME => $column->getName(),
            static::FIELD_IS_INDEXED => $column->isIndexed(),
            static::FIELD_IS_STORED => $column->isStored()
        ];
    }
}