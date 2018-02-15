<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Occurrence;

interface OccurrenceRepository
{
    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public function save(Occurrence $occurrence);
}