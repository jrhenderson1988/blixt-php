<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Position;

class PositionRepository extends Repository
{
    /**
     * Save the given position, updating it in the storage if it already exists, or inserting a new one if it doesn't.
     *
     * @param \Blixt\Persistence\Entities\Position $position
     *
     * @return \Blixt\Persistence\Entities\Position
     */
    public function save(Position $position): Position
    {
        return $position->exists() ? $this->driver()->update($position) : $this->driver()->insert($position);
    }
}