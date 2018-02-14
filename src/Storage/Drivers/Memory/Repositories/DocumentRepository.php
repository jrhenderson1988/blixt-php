<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Document;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\DocumentRepository as DocumentRepositoryInterface;

class DocumentRepository extends AbstractRepository implements DocumentRepositoryInterface
{
    const TABLE = 'documents';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_KEY = 'key';

    /**
     * @inheritdoc
     */
    protected function map($key, array $row)
    {
        return new Document(
            $key,
            $row[static::FIELD_SCHEMA_ID],
            $row[static::FIELD_KEY]
        );
    }

    /**
     * @param int|mixed $key
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function findByKey($key)
    {
        return $this->findBy([
            static::FIELD_KEY => $key
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param int|mixed                      $key
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function create(Schema $schema, $key)
    {
        return $this->insert([
            static::FIELD_SCHEMA_ID => $schema->getId(),
            static::FIELD_KEY => $key
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Document $document
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function save(Document $document)
    {
        // TODO: Implement save() method.
    }
}