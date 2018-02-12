<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Occurrence;
use Blixt\Storage\Entities\Position;

interface PositionRepository
{
    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     * @param int                                $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    public function create(Occurrence $occurrence, $position);

    /**
     * @param \Blixt\Storage\Entities\Position $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    public function save(Position $position);
}