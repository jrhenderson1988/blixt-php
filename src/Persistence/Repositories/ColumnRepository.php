<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Entities\Schema;
use Illuminate\Support\Collection;

/**
 * ColumnRepository.
 *
 * @method Collection get(array $ids)
 * @method Collection getWhere(array $conditions, int $offset = 0, int $limit = null)
 * @method Collection all(int $offset = 0, int $limit = null)
 * @method Column|null findBy(array $conditions)
 * @method Column|null find(int $id)
 * @method Column create(Column $entity)
 * @method Column update(Column $entity)
 * @method Column save(Column $entity)
 *
 * @package Blixt\Persistence\Repositories
 */
class ColumnRepository extends Repository
{
    public const TABLE = 'columns';
    public const SCHEMA_ID = 'schema_id';
    public const NAME = 'name';
    public const IS_INDEXED = 'is_indexed';
    public const IS_STORED = 'is_stored';

    /**
     * Get the attributes from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Column|\Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    public static function toAttributes(Entity $entity): array
    {
        return [
            static::SCHEMA_ID => $entity->getSchemaId(),
            static::NAME => $entity->getName(),
            static::IS_INDEXED => $entity->isIndexed(),
            static::IS_STORED => $entity->isStored()
        ];
    }

    /**
     * Create a relevant entity from the given ID and set of attributes.
     *
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Column|\Blixt\Persistence\Entities\Entity
     */
    public static function toEntity(int $id, array $attributes): Entity
    {
        return Column::make(
            $id,
            $attributes[static::SCHEMA_ID],
            $attributes[static::NAME],
            $attributes[static::IS_INDEXED],
            $attributes[static::IS_STORED]
        );
    }

    /**
     * Get all columns that belong to the given schema.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     *
     * @return Collection
     */
    public function getBySchema(Schema $schema): Collection
    {
        return $this->getWhere([static::SCHEMA_ID => $schema->getId()]);
    }
}
