<?php

namespace Blixt\Storage\Repositories;

interface ColumnRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * @param int|mixed    $schemaId
     * @param string|mixed $name
     * @param bool|mixed   $isIndexed
     * @param bool|mixed   $isStored
     *
     * @return \Blixt\Storage\Entities\Column|null
     */
    public function create($schemaId, $name, $isIndexed, $isStored);
}