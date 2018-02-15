<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Position;

interface PositionRepository
{
    /**
     * @param \Blixt\Storage\Entities\Position $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    public function save(Position $position);
}