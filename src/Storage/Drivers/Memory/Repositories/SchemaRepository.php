<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\SchemaRepository as SchemaRepositoryInterface;

class SchemaRepository extends AbstractRepository implements SchemaRepositoryInterface
{
    const TABLE = 'schemas';
    const FIELD_NAME = 'name';

    /**
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Schema
     */
    protected function map(array $row)
    {
        return new Schema(
            $row[static::FIELD_ID],
            $row[static::FIELD_NAME]
        );
    }

    /**
     * @inheritdoc
     */
    public function create($name)
    {
        return $this->insert([static::FIELD_NAME => $name]);
    }
}