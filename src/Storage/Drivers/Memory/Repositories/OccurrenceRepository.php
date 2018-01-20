<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Occurrence;
use Blixt\Storage\Repositories\OccurrenceRepository as OccurrenceRepositoryInterface;

class OccurrenceRepository extends AbstractRepository implements OccurrenceRepositoryInterface
{
    const TABLE = 'occurrences';
    const FIELD_FIELD_ID = 'field_id';
    const FIELD_TERM_ID = 'term_id';
    const FIELD_FREQUENCY = 'frequency';

    /**
     * Map an array, representing an entity into a relevant Entity object.
     *
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    protected function map(array $row)
    {
        return new Occurrence(
            $row[static::FIELD_ID],
            $row[static::FIELD_FIELD_ID],
            $row[static::FIELD_TERM_ID],
            $row[static::FIELD_FREQUENCY]
        );
    }
}