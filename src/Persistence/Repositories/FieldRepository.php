<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Entities\Field;
use Blixt\Persistence\Entities\Occurrence;
use Illuminate\Support\Collection;

/**
 * FieldRepository.
 *
 * @method Collection get(array $ids)
 * @method Collection getWhere(array $conditions, int $offset = 0, int $limit = null)
 * @method Collection all(int $offset = 0, int $limit = null)
 * @method Field|null findBy(array $conditions)
 * @method Field|null find(int $id)
 * @method Field create(Field $entity)
 * @method Field update(Field $entity)
 * @method Field save(Field $entity)
 *
 * @package Blixt\Persistence\Repositories
 */
class FieldRepository extends Repository
{
    public const TABLE = 'fields';
    public const DOCUMENT_ID = 'document_id';
    public const COLUMN_ID = 'column_id';
    public const VALUE = 'value';

    /**
     * Get the attributes from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Field|\Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    public static function toAttributes(Entity $entity): array
    {
        return [
            static::DOCUMENT_ID => $entity->getDocumentId(),
            static::COLUMN_ID => $entity->getColumnId(),
            static::VALUE => $entity->getValue()
        ];
    }

    /**
     * Create a relevant entity from the given ID and set of attributes.
     *
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Field|\Blixt\Persistence\Entities\Entity
     */
    public static function toEntity(int $id, array $attributes): Entity
    {
        return Field::make(
            $id,
            $attributes[static::DOCUMENT_ID],
            $attributes[static::COLUMN_ID],
            $attributes[static::VALUE]
        );
    }

    /**
     * Get a collection of fields that are referred to by the given collection of occurrences.
     *
     * @param Collection $occurrences
     *
     * @return Collection
     */
    public function getByOccurrences(Collection $occurrences): Collection
    {
        return $this->get($occurrences->map(function (Occurrence $occurrence) {
            return $occurrence->getFieldId();
        })->unique()->all());
    }
}
