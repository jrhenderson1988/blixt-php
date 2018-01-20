<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Position;
use Blixt\Storage\Repositories\PositionRepository as PositionRepositoryInterface;

class PositionRepository extends AbstractRepository implements PositionRepositoryInterface
{
    const TABLE = 'positions';
    const FIELD_OCCURRENCE_ID = 'occurrence_id';
    const FIELD_POSITION = 'position';

    /**
     * Map an array, representing an entity into a relevant Entity object.
     *
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Position
     */
    protected function map(array $row)
    {
        return new Position(
            $row[static::FIELD_ID],
            $row[static::FIELD_OCCURRENCE_ID],
            $row[static::FIELD_POSITION]
        );
    }
}