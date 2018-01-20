<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Term;
use Blixt\Storage\Repositories\TermRepository as TermRepositoryInterface;

class TermRepository extends AbstractRepository implements TermRepositoryInterface
{
    const TABLE = 'terms';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_WORD_ID = 'word_id';
    const FIELD_DOCUMENT_COUNT = 'document_count';

    /**
     * Map an array, representing an entity into a relevant Entity object.
     *
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Term
     */
    protected function map(array $row)
    {
        return new Term(
            $row[static::FIELD_ID],
            $row[static::FIELD_SCHEMA_ID],
            $row[static::FIELD_WORD_ID],
            $row[static::FIELD_DOCUMENT_COUNT]
        );
    }
}