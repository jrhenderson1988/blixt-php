<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Schema;

interface DocumentRepository
{
    /**
     * @param int|mixed $key
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function findByKey($key);

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param int|mixed                      $key
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function create(Schema $schema, $key);
}