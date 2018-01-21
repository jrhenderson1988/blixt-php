<?php

namespace Blixt\Storage\Repositories;

interface SchemaRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * @param string $name
     *
     * @return \Blixt\Storage\Entities\Schema|null
     */
    public function create($name);
}