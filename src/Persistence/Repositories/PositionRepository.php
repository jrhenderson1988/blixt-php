<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Entities\Position;

/**
 * PositionRepository.
 *
 * @method \Illuminate\Support\Collection getWhere(array $conditions, int $offset = 0, int $limit = null)
 * @method \Illuminate\Support\Collection all(int $offset = 0, int $limit = null)
 * @method Position|null findBy(array $conditions)
 * @method Position|null find(int $id)
 * @method Position create(Position $entity)
 * @method Position update(Position $entity)
 * @method Position save(Position $entity)
 *
 * @package Blixt\Persistence\Repositories
 */
class PositionRepository extends Repository
{
    public const TABLE = 'positions';
    public const OCCURRENCE_ID = 'occurrence_id';
    public const POSITION = 'position';

    /**
     * Get the attributes from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Position|\Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    public static function getAttributes(Entity $entity): array
    {
        return [
            static::OCCURRENCE_ID => $entity->getOccurrenceId(),
            static::POSITION => $entity->getPosition()
        ];
    }

    /**
     * Create a relevant entity from the given ID and set of attributes.
     *
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Position|\Blixt\Persistence\Entities\Entity
     */
    public static function toEntity(int $id, array $attributes): Entity
    {
        return Position::make(
            $id,
            $attributes[static::OCCURRENCE_ID],
            $attributes[static::POSITION]
        );
    }
}