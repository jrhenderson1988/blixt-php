<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Entity;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\ColumnRepository as ColumnRepositoryInterface;
use Illuminate\Support\Collection;

class ColumnRepository extends AbstractRepository implements ColumnRepositoryInterface
{
    const ENTITY = Column::class;
    const TABLE = 'columns';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_NAME = 'name';
    const FIELD_IS_INDEXED = 'is_indexed';
    const FIELD_IS_STORED = 'is_stored';

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Illuminate\Support\Collection
     * @throws \Blixt\Exceptions\StorageException
     */
    public function getBySchema(Schema $schema): Collection
    {
        return $this->getEntitiesBy([
            static::FIELD_SCHEMA_ID => $schema->getId()
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return \Blixt\Storage\Entities\Column
     * @throws \Blixt\Exceptions\StorageException
     */
    public function save(Column $column): Column
    {
        return $this->saveEntity($column);
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
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return array
     */
    protected function getAttributes(Entity $entity)
    {
        return [
            static::FIELD_SCHEMA_ID => $entity->getSchemaId(),
            static::FIELD_NAME => $entity->getName(),
            static::FIELD_IS_INDEXED => $entity->isIndexed(),
            static::FIELD_IS_STORED => $entity->isStored()
        ];
    }
}