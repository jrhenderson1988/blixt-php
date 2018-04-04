<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Occurrence;

class OccurrenceRepository extends Repository
{
    /**
     * Save the given occurrence, updating it in the storage if it already exists, or inserting a new one if it doesn't.
     *
     * @param \Blixt\Persistence\Entities\Occurrence $occurrence
     *
     * @return \Blixt\Persistence\Entities\Occurrence
     */
    public function save(Occurrence $occurrence): Occurrence
    {
        return $occurrence->exists() ? $this->driver()->update($occurrence) : $this->driver()->insert($occurrence);
    }
}