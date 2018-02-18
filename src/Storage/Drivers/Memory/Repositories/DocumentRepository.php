<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Document;
use Blixt\Storage\Entities\Entity;
use Blixt\Storage\Repositories\DocumentRepository as DocumentRepositoryInterface;

class DocumentRepository extends AbstractRepository implements DocumentRepositoryInterface
{
    const ENTITY = Document::class;
    const TABLE = 'documents';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_KEY = 'key';

    /**
     * @param int|mixed $key
     *
     * @return \Blixt\Storage\Entities\Document|null
     * @throws \Blixt\Exceptions\StorageException
     */
    public function findByKey($key)
    {
        return $this->findEntityBy([
            static::FIELD_KEY => $key
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Document $document
     *
     * @return \Blixt\Storage\Entities\Document
     * @throws \Blixt\Exceptions\StorageException
     */
    public function save(Document $document)
    {
        return $this->saveEntity($document);
    }

    /**
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Document
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
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return array
     */
    protected function getAttributes(Entity $entity)
    {
        return [
            static::FIELD_SCHEMA_ID => $entity->getSchemaId(),
            static::FIELD_KEY => $entity->getKey()
        ];
    }
}