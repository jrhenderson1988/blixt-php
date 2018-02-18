<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Entity;
use Blixt\Storage\Entities\Position;
use Blixt\Storage\Repositories\PositionRepository as PositionRepositoryInterface;

class PositionRepository extends AbstractRepository implements PositionRepositoryInterface
{
    const ENTITY = Position::class;
    const TABLE = 'positions';
    const FIELD_OCCURRENCE_ID = 'occurrence_id';
    const FIELD_POSITION = 'position';

    /**
     * @param \Blixt\Storage\Entities\Position $position
     *
     * @return \Blixt\Storage\Entities\Position
     * @throws \Blixt\Exceptions\StorageException
     */
    public function save(Position $position)
    {
        return $this->saveEntity($position);
    }

    /**
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Position
     */
    protected function map($key, array $row)
    {
        return new Position(
            $key,
            $row[static::FIELD_OCCURRENCE_ID],
            $row[static::FIELD_POSITION]
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
            static::FIELD_OCCURRENCE_ID => $entity->getOccurrenceId(),
            static::FIELD_POSITION => $entity->getPosition()
        ];
    }
}