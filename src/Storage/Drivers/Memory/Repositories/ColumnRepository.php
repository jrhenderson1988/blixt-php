<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Column;
use Blixt\Storage\Repositories\ColumnRepository as ColumnRepositoryInterface;

class ColumnRepository extends AbstractRepository implements ColumnRepositoryInterface
{
    const TABLE = 'columns';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_NAME = 'name';
    const FIELD_IS_INDEXED = 'is_indexed';
    const FIELD_IS_STORED = 'is_stored';

    /**
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Column
     */
    protected function map(array $row)
    {
        return new Column(
            $row[static::FIELD_ID],
            $row[static::FIELD_SCHEMA_ID],
            $row[static::FIELD_NAME],
            $row[static::FIELD_IS_INDEXED],
            $row[static::FIELD_IS_STORED]
        );
    }

    /**
     * @param int|mixed    $schemaId
     * @param string|mixed $name
     * @param bool|mixed   $isIndexed
     * @param bool|mixed   $isStored
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function create($schemaId, $name, $isIndexed, $isStored)
    {
        return $this->insert([
            static::FIELD_SCHEMA_ID => $schemaId,
            static::FIELD_NAME => $name,
            static::FIELD_IS_INDEXED => $isIndexed,
            static::FIELD_IS_STORED => $isStored
        ]);
    }
}