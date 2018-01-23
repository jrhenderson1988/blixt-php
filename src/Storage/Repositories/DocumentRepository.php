<?php

namespace Blixt\Storage\Repositories;

interface DocumentRepository
{
    /**
     * @param int|mixed $key
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function findByKey($key);

    /**
     * @param int       $schemaId
     * @param int|mixed $key
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function create($schemaId, $key);
}