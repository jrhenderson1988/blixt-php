<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Document;
use Blixt\Storage\Repositories\DocumentRepository as DocumentRepositoryInterface;

class DocumentRepository implements DocumentRepositoryInterface
{
    const TABLE = 'documents';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_KEY = 'key';

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /**
     * DocumentRepository constructor.
     *
     * @param \Blixt\Storage\Drivers\Memory\Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param int|mixed $key
     *
     * @return \Blixt\Storage\Entities\Document|null
     */
    public function findByKey($key)
    {
        $items = $this->storage->getWhere(static::TABLE, [
            static::FIELD_KEY => $key
        ]);

        if (count($items) > 0) {
            reset($items);

            return $this->map($id = key($items), $items[$id]);
        }

        return null;
    }

    /**
     * @param \Blixt\Storage\Entities\Document $document
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function save(Document $document)
    {
        return $document->exists() ? $this->update($document) : $this->create($document);
    }

    /**
     * @param \Blixt\Storage\Entities\Document $document
     *
     * @return \Blixt\Storage\Entities\Document
     */
    protected function create(Document $document)
    {
        $attributes = $this->getAttributes($document);

        $id = $this->storage->insert(static::TABLE, $attributes);

        return $this->map($id, $attributes);
    }

    /**
     * @param \Blixt\Storage\Entities\Document $document
     *
     * @return \Blixt\Storage\Entities\Document
     */
    protected function update(Document $document)
    {
        $attributes = $this->getAttributes($document);

        $this->storage->update(static::TABLE, $document->getId(), $attributes);

        return $document;
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
     * @param \Blixt\Storage\Entities\Document $entity
     *
     * @return array
     */
    protected function getAttributes(Document $entity)
    {
        return [
            static::FIELD_SCHEMA_ID => $entity->getSchemaId(),
            static::FIELD_KEY => $entity->getKey()
        ];
    }
}