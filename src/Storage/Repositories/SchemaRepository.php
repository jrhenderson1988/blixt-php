<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Schema;

interface SchemaRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Blixt\Storage\Entities\Schema
     */
    public function save(Schema $schema);
}