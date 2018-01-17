<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Document;
use Blixt\Storage\Entities\Schema;
use Illuminate\Support\Collection;

interface DocumentRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Illuminate\Support\Collection
     */
    public function allForSchema(Schema $schema);

    /**
     * @param \Illuminate\Support\Collection $schemas
     *
     * @return \Illuminate\Support\Collection
     */
    public function allForSchemas(Collection $schemas);

    /**
     * @param int|mixed $id
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function find($id);

    /**
     * @param string|mixed $name
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function findByKey($name);

    /**
     * @param \Blixt\Storage\Entities\Document $document
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function save(Document $document);
}