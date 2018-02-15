<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Document;

interface DocumentRepository
{
    /**
     * @param int|mixed $key
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function findByKey($key);

    /**
     * @param \Blixt\Storage\Entities\Document $document
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function save(Document $document);
}