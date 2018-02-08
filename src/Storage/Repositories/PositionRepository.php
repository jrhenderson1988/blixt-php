<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Occurrence;

interface PositionRepository
{
    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     * @param int                                $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    public function create(Occurrence $occurrence, $position);
}