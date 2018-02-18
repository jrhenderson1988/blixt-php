<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Entity;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\SchemaRepository as SchemaRepositoryInterface;

class SchemaRepository extends AbstractRepository implements SchemaRepositoryInterface
{
    const ENTITY = Schema::class;
    const TABLE = 'schemas';
    const FIELD_NAME = 'name';

    /**
     * @return \Illuminate\Support\Collection
     * @throws \Blixt\Exceptions\StorageException
     */
    public function all()
    {
        return $this->allEntities();
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Blixt\Storage\Entities\Schema
     * @throws \Blixt\Exceptions\StorageException
     */
    public function save(Schema $schema)
    {
        return $this->saveEntity($schema);
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
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return array
     */
    protected function getAttributes(Entity $entity)
    {
        return [
            static::FIELD_NAME => $entity->getName()
        ];
    }
}