<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Occurrence;
use Blixt\Storage\Entities\Position;
use Blixt\Storage\Repositories\PositionRepository as PositionRepositoryInterface;

class PositionRepository extends AbstractRepository implements PositionRepositoryInterface
{
    const TABLE = 'positions';
    const FIELD_OCCURRENCE_ID = 'occurrence_id';
    const FIELD_POSITION = 'position';

    /**
     * @inheritdoc
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
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     * @param int                                $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    public function create(Occurrence $occurrence, $position)
    {
        return $this->insert([
            static::FIELD_OCCURRENCE_ID => $occurrence->getId(),
            static::FIELD_POSITION => $position
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Position $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    public function save(Position $position)
    {
        // TODO: Implement save() method.
    }
}