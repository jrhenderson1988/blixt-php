<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Entities\Schema;

/**
 * SchemaRepository.
 *
 * @method \Illuminate\Support\Collection getWhere(array $conditions, int $offset = 0, int $limit = null)
 * @method \Illuminate\Support\Collection all(int $offset = 0, int $limit = null)
 * @method Schema|null findBy(array $conditions)
 * @method Schema|null find(int $id)
 * @method Schema create(Schema $entity)
 * @method Schema update(Schema $entity)
 * @method Schema save(Schema $entity)
 *
 * @package Blixt\Persistence\Repositories
 */
class SchemaRepository extends Repository
{
    public const TABLE = 'schemas';
    public const NAME = 'name';

    /**
     * Get the attributes from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Schema|\Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    public static function getAttributes(Entity $entity): array
    {
        return [
            static::NAME => $entity->getName()
        ];
    }

    /**
     * Create a relevant entity from the given ID and set of attributes.
     *
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Schema|\Blixt\Persistence\Entities\Entity
     */
    public static function toEntity(int $id, array $attributes): Entity
    {
        return Schema::make(
            $id,
            $attributes[static::NAME]
        );
    }

    /**
     * Find a schema by the given name.
     *
     * @param string $name
     *
     * @return \Blixt\Persistence\Entities\Schema|null
     */
    public function findByName(string $name): ?Schema
    {
        return $this->findBy([static::NAME => $name]);
    }
}