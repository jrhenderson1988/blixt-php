<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\SchemaRepository as SchemaRepositoryInterface;
use Illuminate\Support\Collection;

class SchemaRepository implements SchemaRepositoryInterface
{
    const TABLE = 'schemas';
    const FIELD_NAME = 'name';

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /**
     * SchemaRepository constructor.
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
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Blixt\Storage\Entities\Schema
     */
    public function save(Schema $schema)
    {
        return $schema->exists() ? $this->update($schema) : $this->create($schema);
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Blixt\Storage\Entities\Schema
     */
    protected function create(Schema $schema)
    {
        $attributes = $this->getAttributes($schema);

        $id = $this->storage->insert(static::TABLE, $attributes);

        return $this->map($id, $attributes);
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Blixt\Storage\Entities\Schema
     */
    protected function update(Schema $schema)
    {
        $attributes = $this->getAttributes($schema);

        $this->storage->update(static::TABLE, $schema->getId(), $attributes);

        return $schema;
    }

    /**
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Schema
     */
    protected function map($key, array $row)
    {
        return new Schema(
            $key,
            $row[static::FIELD_NAME]
        );
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return array
     */
    protected function getAttributes(Schema $schema)
    {
        return [
            static::FIELD_NAME => $schema->getName()
        ];
    }
}