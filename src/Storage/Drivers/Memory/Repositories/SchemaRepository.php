<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\SchemaRepository as SchemaRepositoryInterface;

class SchemaRepository extends AbstractRepository implements SchemaRepositoryInterface
{
    const TABLE = 'schemas';
    const FIELD_NAME = 'name';

    /**
     * @inheritdoc
     */
    protected function map($key, array $row)
    {
        return new Schema(
            $key,
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

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Blixt\Storage\Entities\Schema
     */
    public function save(Schema $schema)
    {
        // TODO: Implement save() method.
    }
}