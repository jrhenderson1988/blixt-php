<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Document;

class DocumentRepository extends Repository
{
    /**
     * Find a document by its given key.
     *
     * @param mixed $key
     *
     * @return \Blixt\Persistence\Entities\Document|null
     */
    public function findByKey($key): ?Document
    {
        return $this->driver()->findBy(Document::class, [Document::FIELD_KEY => $key]);
    }

    /**
     * Save the given document, updating it in the storage if it already exists, or inserting a new one if it doesn't.
     *
     * @param \Blixt\Persistence\Entities\Document $document
     *
     * @return \Blixt\Persistence\Entities\Document|null
     */
    public function save(Document $document): ?Document
    {
        return $document->exists() ? $this->driver()->update($document) : $this->driver()->insert($document);
    }
}