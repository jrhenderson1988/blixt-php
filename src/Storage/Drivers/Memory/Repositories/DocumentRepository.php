<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Document;
use Blixt\Storage\Repositories\DocumentRepository as DocumentRepositoryInterface;

class DocumentRepository extends AbstractRepository implements DocumentRepositoryInterface
{
    const TABLE = 'documents';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_KEY = 'key';

    /**
     * Map an array, representing an entity into a relevant Entity object.
     *
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Document
     */
    protected function map(array $row)
    {
        return new Document(
            $row[static::FIELD_ID],
            $row[static::FIELD_SCHEMA_ID],
            $row[static::FIELD_KEY]
        );
    }
}