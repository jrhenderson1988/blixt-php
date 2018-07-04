<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Document;
use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Entities\Schema;

/**
 * DocumentRepository.
 *
 * @method \Illuminate\Support\Collection getWhere(array $conditions, int $offset = 0, int $limit = null)
 * @method \Illuminate\Support\Collection all(int $offset = 0, int $limit = null)
 * @method Document|null findBy(array $conditions)
 * @method Document|null find(int $id)
 * @method Document create(Document $entity)
 * @method Document update(Document $entity)
 * @method Document save(Document $entity)
 *
 * @package Blixt\Persistence\Repositories
 */
class DocumentRepository extends Repository
{
    public const TABLE = 'documents';
    public const SCHEMA_ID = 'schema_id';
    public const KEY = 'key';

    /**
     * Get the attributes from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Document|\Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    public static function toAttributes(Entity $entity): array
    {
        return [
            static::SCHEMA_ID => $entity->getSchemaId(),
            static::KEY => $entity->getKey()
        ];
    }

    /**
     * Create a relevant entity from the given ID and set of attributes.
     *
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Document|\Blixt\Persistence\Entities\Entity
     */
    public static function toEntity(int $id, array $attributes): Entity
    {
        return Document::make(
            $id,
            $attributes[static::SCHEMA_ID],
            $attributes[static::KEY]
        );
    }

    /**
     * Find a document by its given key.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     * @param mixed $key
     *
     * @return \Blixt\Persistence\Entities\Document|\Blixt\Persistence\Entities\Entity|null
     */
    public function findBySchemaAndKey(Schema $schema, $key): ?Entity
    {
        return $this->findBy([
            static::SCHEMA_ID => $schema->getId(),
            static::KEY => $key
        ]);
    }
}